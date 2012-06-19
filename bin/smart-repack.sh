#!/bin/bash

export GIT="/usr/bin/env git"
export CITTERN="/usr/bin/env cittern"

$GIT repack -A -d
branches=()
eval "$($GIT for-each-ref --shell --format='branches+=(%(refname))' refs/heads/)"
for branch in "${branches[@]}"; do
  shortname=${branch#refs\/heads\/}
  $CITTERN repo:unpack-nearby $shortname
done