cd ..;
git pull;
git status;
cd joomla-module;
zip -x "*.git*" -x "*.DS_Store" -r ~/joomla-module.zip *;
cd ..;
zip -x "*.git*" -x "*.DS_Store" -r ~/joomla-component.zip \
component \
index.html \
joomla-component.xml \
license.txt;
