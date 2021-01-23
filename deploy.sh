set -e

bin/phpunit

(git push) || true

git checkout production
git merge master -m "Sending"

git push origin production

git checkout master
