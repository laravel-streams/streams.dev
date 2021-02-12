set -e

bin/phpunit

(git push) || true

git checkout production
git merge master -m "CI/CD"

git push origin production

git checkout master
