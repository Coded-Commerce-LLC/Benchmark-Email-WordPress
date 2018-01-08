cd ../;
git pull;
git status;
cd ../;
zip \
	-x "*.git*" \
	-x "*.DS_Store" \
	-x "benchmark-email-lite/build_scripts*" \
	-x "benchmark-email-lite/component/assets/lib*" \
	-x "benchmark-email-lite/component/benchmarkemaillite.php" \
	-x "benchmark-email-lite/component/config.xml" \
	-x "benchmark-email-lite/component/language*" \
	-x "benchmark-email-lite/component/controllers/dashboard.php" \
	-x "benchmark-email-lite/component/controllers/joomla-wp-bootup.php" \
	-x "benchmark-email-lite/joomla-*" \
	-x "benchmark-email-lite/pkg_*" \
	-r ~/benchmark-email-lite.zip \
	benchmark-email-lite;
