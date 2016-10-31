cd ..;
git pull;
git status;

mkdir packages;

cd joomla-module;
zip -x "*.git*" -x "*.DS_Store" -r ../packages/mod_benchmarkemaillite.zip *;

cd ..;
zip -x "*.git*" -x "*.DS_Store" -r packages/com_benchmarkemaillite.zip \
component \
index.html \
joomla-component.xml \
license.txt;

zip -r pkg_benchmarkemaillite.zip pkg_benchmarkemaillite.xml packages;

rm packages/com_benchmarkemaillite.zip;
rm packages/mod_benchmarkemaillite.zip;
rmdir packages;
