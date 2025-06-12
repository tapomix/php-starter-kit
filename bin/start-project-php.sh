#!/bin/bash

set -e # stop on error

STARTER_GIT="${STARTER_GIT:-git@github.com:tapomix/php-starter-kit.git}"

echo "Tapomix / Starter Kit # PHP"
echo "Loading from ${STARTER_GIT} ..."

# retrieve list of branches like dev-*
BRANCHES_LIST=$(git ls-remote --branches "$STARTER_GIT" dev-*)
# remove long names, sort
BRANCHES=$(echo "$BRANCHES_LIST" \
    | sed 's|.*refs/heads/||' \
    | sort -u
)

if [[ -z "$BRANCHES" ]]; then
    echo "❌ No branches found."
    exit 1
fi

DEFAULT_BRANCH=$(echo "$BRANCHES" | head -n 1)

# ask for project name
read -rp "Project name : " PROJECT

# validate project name + folder
if [[ ! "$PROJECT" =~ ^([a-z0-9]+)([.-][a-z0-9]+)*$ ]]; then
    echo "❌ Invalid project name."
    exit 1
fi

if [[ -d "$PROJECT" ]]; then
    echo "❌ Directory already exists."
    exit 1
fi

# select a branch from the list
echo "Available branches :"
echo "$BRANCHES"

while true; do
    read -rp "Branch to clone ? (default: $DEFAULT_BRANCH) " BRANCH
    BRANCH=${BRANCH:-$DEFAULT_BRANCH}

    if echo "$BRANCHES" | grep -qx "$BRANCH"; then
        break
    else
        echo "❌ Unknown branch, please select one in the list."
    fi
done

git clone --branch "$BRANCH" --single-branch "$STARTER_GIT" "$PROJECT"

cd "$PROJECT"

# clean git to allow creation of a fresh repo
rm -rf .git

if [[ -f .gitignore.example ]]; then
    mv .gitignore.example .gitignore
fi

echo "Project '$PROJECT' initialized !"
