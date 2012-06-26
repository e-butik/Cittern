#!/bin/bash

export GIT="/usr/bin/env git"
export CITTERN="/usr/bin/env cittern"

$GIT repack -A -d
if [ $? -ne 0 ]; then
  exit 1
fi

branches=()
eval "$($GIT for-each-ref --shell --format='branches+=(%(refname))' refs/heads/)"
for branch in "${branches[@]}"; do
  shortname=${branch#refs\/heads\/}
  echo "Unpacking $shortname"
  $CITTERN repo:unpack-nearby $shortname

  if [ $? -ne 0 ]; then
    exit 1
  fi
done