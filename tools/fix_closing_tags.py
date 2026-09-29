from pathlib import Path

root = Path(r'c:\xampp\htdocs\backend\godashop')

for p in root.rglob('*.php'):
    rel = p.relative_to(root)
    if 'vendor' in rel.parts:
        continue
    try:
        text = p.read_text(encoding='utf-8')
    except UnicodeDecodeError:
        text = p.read_text(encoding='latin1')

    new = text.rstrip()
    if new.endswith('?>'):
        new = new[:-2].rstrip()

    if new != text:
        p.write_text(new + ('\n' if not new.endswith('\n') else ''), encoding='utf-8')
        print(p)
