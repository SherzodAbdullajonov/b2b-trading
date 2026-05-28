#!/usr/bin/env bash
# Step 2 — scaffold a fresh Laravel 11 install INTO the existing project folder,
# preserving our .git history, README.md, .gitignore, and docs/.
#
# Run this on your Mac from anywhere (PHP 8.2+ and Composer must be installed):
#   bash ~/Downloads/Projects/b2b-trading-api/scripts/scaffold-laravel.sh
#
# Idempotent-ish: refuses to run if Laravel files already exist.

set -euo pipefail

PROJECT_DIR="$HOME/Downloads/Projects/b2b-trading-api"
SCAFFOLD_DIR="$HOME/Downloads/Projects/_b2b_scaffold_tmp"

if [[ ! -d "$PROJECT_DIR/.git" ]]; then
  echo "ERROR: $PROJECT_DIR does not look like our project (no .git)." >&2
  exit 1
fi

if [[ -f "$PROJECT_DIR/artisan" ]]; then
  echo "ERROR: artisan already exists in $PROJECT_DIR — Laravel is already installed." >&2
  exit 1
fi

command -v composer >/dev/null 2>&1 || { echo "ERROR: composer not found in PATH." >&2; exit 1; }
command -v php      >/dev/null 2>&1 || { echo "ERROR: php not found in PATH." >&2; exit 1; }

echo "==> Using PHP: $(php -r 'echo PHP_VERSION;')"
echo "==> Using Composer: $(composer --version | head -1)"

# 1. Fresh Laravel 11 install into a sibling temp folder
rm -rf "$SCAFFOLD_DIR"
echo "==> composer create-project laravel/laravel:^11.0 ($SCAFFOLD_DIR)"
composer create-project --prefer-dist laravel/laravel:^11.0 "$SCAFFOLD_DIR"

# 2. Throw away Laravel's stock README and .gitignore — we keep ours
rm -f "$SCAFFOLD_DIR/README.md" "$SCAFFOLD_DIR/.gitignore"

# 3. Move every other file (regular + hidden) into the project folder
echo "==> Merging scaffold into $PROJECT_DIR"
shopt -s dotglob nullglob
for entry in "$SCAFFOLD_DIR"/*; do
  base=$(basename "$entry")
  # never overwrite our protected files
  case "$base" in
    .git|README.md|.gitignore|docs|scripts) continue ;;
  esac
  mv "$entry" "$PROJECT_DIR/$base"
done
shopt -u dotglob nullglob

rmdir "$SCAFFOLD_DIR"

# 4. Set up .env for local MySQL
cd "$PROJECT_DIR"
cp .env.example .env

# Patch the DB block in .env to MySQL + our db name (use a temp file to be sed-portable on macOS)
python3 - <<'PY'
from pathlib import Path
p = Path(".env")
text = p.read_text().splitlines()
out = []
for line in text:
    if line.startswith("DB_CONNECTION="):
        out.append("DB_CONNECTION=mysql")
    elif line.startswith("# DB_HOST=") or line.startswith("DB_HOST="):
        out.append("DB_HOST=127.0.0.1")
    elif line.startswith("# DB_PORT=") or line.startswith("DB_PORT="):
        out.append("DB_PORT=3306")
    elif line.startswith("# DB_DATABASE=") or line.startswith("DB_DATABASE="):
        out.append("DB_DATABASE=b2b_trading")
    elif line.startswith("# DB_USERNAME=") or line.startswith("DB_USERNAME="):
        out.append("DB_USERNAME=root")
    elif line.startswith("# DB_PASSWORD=") or line.startswith("DB_PASSWORD="):
        out.append("DB_PASSWORD=")
    elif line.startswith("APP_NAME="):
        out.append('APP_NAME="B2B Trading API"')
    else:
        out.append(line)
p.write_text("\n".join(out) + "\n")
print("Patched .env for MySQL @ b2b_trading")
PY

# 5. Generate APP_KEY
php artisan key:generate

# 6. Sanity-check: confirm artisan works
php artisan --version

echo
echo "==> Done. Next:"
echo "   1) Make sure MySQL is running and the database exists:"
echo "        mysql -uroot -e 'CREATE DATABASE IF NOT EXISTS b2b_trading CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;'"
echo "   2) cd $PROJECT_DIR && php artisan migrate"
echo "   3) Tell Claude that scaffold is done so we can commit Step 2."
