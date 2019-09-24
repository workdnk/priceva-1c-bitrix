#!/bin/bash

# rm unnecessary files

echo "Removing .git"
rm -Rf .git
echo "Removing docs"
rm -Rf docs
echo "Removing utils"
rm -Rf utils
echo "Removing .gitignore"
rm -f .gitignore
echo "Removing LICENSE"
rm -f LICENSE
echo "Removing README.md"
rm -f README.md

# Recursive file convertion windows-1251 --> utf-8
# Place this file in the root of your site, add execute permission and run
# Converts *.php, *.html, *.css, *.js files.
# To add file type by extension, e.g. *.cgi, add '-o -name "*.cgi"' to the find command

find ./ -name "*.php" -o -name "*.html" -o -name "*.css" -o -name "*.js" -o -name "*.md" -o -name "*.ru" -o -name "*.en" -type f |
while read file
do
  if [[ "$file" == *"description."* ]];then
    continue
  fi
  echo " $file"
  mv "$file" "$file".icv
  iconv -f UTF-8 -t WINDOWS-1251 "$file".icv > "$file"
  rm -f "$file".icv
done
