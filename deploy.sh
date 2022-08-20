# Show Exceptions
set -e

# Run Tests
bin/phpunit

(git push) || true

git checkout production
git merge master -m "CI/CD"

git push origin production

## Done
git checkout master
