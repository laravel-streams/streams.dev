set -e

bin/phpunit

(git push) || true

git checkout production
git merge master -m "CD"

git push origin production

git checkout master
