import os
import re
import json
from pathlib import Path

BASE_DIR = Path(r"d:\Project PKL\SignalNiagaJasaTender")
RAW_DIR = Path(r"d:\Project PKL\qa-reports\whitebox\raw")

def get_php_files(directory):
    for root, _, files in os.walk(directory):
        for f in files:
            if f.endswith('.php'):
                yield Path(root) / f

def analyze_complexity_and_metrics():
    func_pattern = re.compile(r'^\s*(?:public|protected|private|static|\s)*function\s+([a-zA-Z0-9_]+)\s*\((.*?)\)', re.MULTILINE)
    
    complexity_keywords = [
        r'\bif\b', r'\belseif\b', r'\bfor\b', r'\bforeach\b',
        r'\bwhile\b', r'\bcase\b', r'\bcatch\b',
        r'&&', r'\|\|', r'\?\?', r'\?'
    ]
    compiled_keywords = [re.compile(kw) for kw in complexity_keywords]

    results = []
    file_metrics = []
    
    scan_dirs = {
        'Controllers': BASE_DIR / 'app' / 'Http' / 'Controllers',
        'Models': BASE_DIR / 'app' / 'Models',
        'Services': BASE_DIR / 'app' / 'Services',
        'Middleware': BASE_DIR / 'app' / 'Http' / 'Middleware',
        'Commands': BASE_DIR / 'app' / 'Console' / 'Commands',
        'Migrations': BASE_DIR / 'database' / 'migrations',
        'Seeders': BASE_DIR / 'database' / 'seeders',
        'Routes': BASE_DIR / 'routes',
        'Views': BASE_DIR / 'resources' / 'views'
    }

    category_stats = {}

    for cat, p in scan_dirs.items():
        if not p.exists():
            continue
        cat_loc = 0
        cat_files = 0
        for f in p.glob('**/*'):
            if f.is_file() and (f.suffix in ['.php', '.blade.php'] or f.name.endswith('.blade.php')):
                cat_files += 1
                try:
                    lines = f.read_text(encoding='utf-8', errors='ignore').splitlines()
                    cat_loc += len(lines)
                except Exception:
                    pass
        category_stats[cat] = {'files': cat_files, 'loc': cat_loc}

    # Detailed complexity on app/
    app_dir = BASE_DIR / 'app'
    for php_file in get_php_files(app_dir):
        rel_path = php_file.relative_to(BASE_DIR).as_posix()
        try:
            content = php_file.read_text(encoding='utf-8', errors='ignore')
        except Exception:
            continue

        lines = content.splitlines()
        total_lines = len(lines)
        code_lines = 0
        comment_lines = 0
        blank_lines = 0
        in_block_comment = False

        for line in lines:
            stripped = line.strip()
            if not stripped:
                blank_lines += 1
                continue
            if in_block_comment:
                comment_lines += 1
                if '*/' in stripped:
                    in_block_comment = False
                continue
            if stripped.startswith('/*'):
                comment_lines += 1
                if '*/' not in stripped:
                    in_block_comment = True
                continue
            if stripped.startswith('//') or stripped.startswith('#'):
                comment_lines += 1
                continue
            code_lines += 1

        file_metrics.append({
            'file': rel_path,
            'total_lines': total_lines,
            'code_lines': code_lines,
            'comment_lines': comment_lines,
            'blank_lines': blank_lines
        })

        func_starts = []
        for idx, line in enumerate(lines):
            m = re.search(r'function\s+([a-zA-Z0-9_]+)\s*\(', line)
            if m:
                func_name = m.group(1)
                func_starts.append((idx, func_name))

        for i, (start_idx, func_name) in enumerate(func_starts):
            end_idx = func_starts[i+1][0] if i+1 < len(func_starts) else total_lines
            func_body = "\n".join(lines[start_idx:end_idx])
            
            # calculate cyclomatic complexity
            cc = 1
            for kw_re in compiled_keywords:
                matches = kw_re.findall(func_body)
                cc += len(matches)

            results.append({
                'file': rel_path,
                'function': func_name,
                'line': start_idx + 1,
                'complexity': cc,
                'loc': end_idx - start_idx
            })

    return category_stats, file_metrics, results

def scan_secrets_and_vulnerabilities():
    findings = []
    
    secret_patterns = [
        (re.compile(r'(?i)(?:api_key|apikey|secret|password|access_token|private_key)\s*=\s*[\'"][a-zA-Z0-9\-_]{16,}[\'"]'), 'Hardcoded Secret/Token', 'High', 'Keamanan'),
        (re.compile(r'-----BEGIN\s+(?:RSA\s+)?PRIVATE\s+KEY-----'), 'Private Key in File', 'Critical', 'Keamanan'),
        (re.compile(r'(?i)AWS_SECRET_ACCESS_KEY\s*=\s*[a-zA-Z0-9\+/]{30,}'), 'AWS Secret Key', 'Critical', 'Keamanan'),
    ]

    sql_patterns = [
        (re.compile(r'DB::raw\s*\(\s*["\'].*?\$(?:request|_[A-Z]+|[a-zA-Z0-9_]+)'), 'Raw SQL with direct variable interpolation', 'Critical', 'Keamanan'),
        (re.compile(r'whereRaw\s*\(\s*["\'].*?\$(?:request|_[A-Z]+|[a-zA-Z0-9_]+)'), 'whereRaw with direct variable interpolation', 'Critical', 'Keamanan'),
    ]

    for scan_dir in [BASE_DIR / 'app', BASE_DIR / 'routes', BASE_DIR / 'config']:
        for php_file in get_php_files(scan_dir):
            rel_path = php_file.relative_to(BASE_DIR).as_posix()
            try:
                lines = php_file.read_text(encoding='utf-8', errors='ignore').splitlines()
            except Exception:
                continue

            for idx, line in enumerate(lines):
                line_no = idx + 1
                for pat, desc, sev, cat in secret_patterns:
                    if pat.search(line):
                        findings.append({
                            'severity': sev,
                            'category': cat,
                            'file_line': f"{rel_path}:{line_no}",
                            'description': f"{desc}: `{line.strip()[:60]}`",
                            'impact': 'Potensi kebocoran kredensial atau token akses sistem.',
                            'suggestion': 'Gunakan environment variable (.env) dan config() helper.',
                            'source': 'Static Analysis (Regex Pattern)',
                            'status': 'Terverifikasi'
                        })
                for pat, desc, sev, cat in sql_patterns:
                    if pat.search(line):
                        findings.append({
                            'severity': sev,
                            'category': cat,
                            'file_line': f"{rel_path}:{line_no}",
                            'description': f"{desc}: `{line.strip()[:70]}`",
                            'impact': 'Risiko SQL Injection jika input pengguna tidak di-sanitize atau di-bind.',
                            'suggestion': 'Gunakan parameterized bindings atau Query Builder bawaan Eloquent.',
                            'source': 'SAST (SQLi Pattern Matcher)',
                            'status': 'Terverifikasi'
                        })
                
                if re.search(r'catch\s*\([^{]+\)\s*\{\s*\}', line):
                    findings.append({
                        'severity': 'Medium',
                        'category': 'Bug',
                        'file_line': f"{rel_path}:{line_no}",
                        'description': 'Empty catch block suppresses exceptions without logging or handling.',
                        'impact': 'Kesalahan/exception ditelan diam-diam, menyulitkan debugging dan investigasi audit.',
                        'suggestion': 'Catat exception ke Log::error($e->getMessage()) atau teruskan pesan yang relevan.',
                        'source': 'Code Review (Pattern)',
                        'status': 'Terverifikasi'
                    })

                if re.search(r'\b(?:TODO|FIXME|XXX|HACK)\b', line, re.IGNORECASE):
                    findings.append({
                        'severity': 'Low',
                        'category': 'Maintainability',
                        'file_line': f"{rel_path}:{line_no}",
                        'description': f"Marker ditemukan: {line.strip()[:60]}",
                        'impact': 'Terdapat implementasi belum tuntas atau catatan teknis yang tertunda.',
                        'suggestion': 'Selesaikan TODO atau bersihkan catatan bila fitur sudah selesai.',
                        'source': 'Static Analysis (Marker Scan)',
                        'status': 'Terverifikasi'
                    })

    return findings

def main():
    cat_stats, file_metrics, func_complexities = analyze_complexity_and_metrics()
    findings = scan_secrets_and_vulnerabilities()
    
    high_cc = [f for f in func_complexities if f['complexity'] > 10]
    high_cc.sort(key=lambda x: x['complexity'], reverse=True)

    summary_data = {
        'category_stats': cat_stats,
        'high_complexity_count': len(high_cc),
        'high_complexity_functions': high_cc,
        'findings': findings
    }

    out_file = RAW_DIR / 'analysis_summary.json'
    out_file.write_text(json.dumps(summary_data, indent=2), encoding='utf-8')
    print(f"Analysis completed: {len(high_cc)} high-complexity functions, {len(findings)} findings.")
    print("Top 10 complex functions:")
    for f in high_cc[:10]:
        print(f"  - {f['file']}:{f['function']} (CC: {f['complexity']}, LOC: {f['loc']})")

if __name__ == '__main__':
    main()
