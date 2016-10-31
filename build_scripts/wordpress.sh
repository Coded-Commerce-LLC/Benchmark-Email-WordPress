cd ..;
git pull;
git status;
zip -x "*.git*" -x "*.DS_Store" -r wordpress-plugin.zip \
component \
index.html \
languages \
license.txt \
readme.txt \
wordpress-plugin.php;
