module.exports = {
	root: true,
	env: {
		'browser': true,
		'es2021': true,
	},
	extends: [
		'eslint:recommended',
		'plugin:tailwindcss/recommended',
	],
	overrides: [
		{
			env: {
				'node': true
			},
			files: [
				'.eslintrc.{js,cjs}'
			],
			parserOptions: {
				sourceType: 'script'
			}
		},
		{
			files: [ '*.html', '*.blade.php' ],
			parser: '@angular-eslint/template-parser',
		},
	],
	parserOptions: {
		ecmaVersion: 'latest',
		sourceType: 'module'
	},
	globals: {
		'$': true,
		'jQuery': true,
		'_': true,
		'toastr': true,
		'Alpine': true,
		'tinymce': true,
		'tinyMCE': true,
		'magicai_localize': true,
		'fetchEventSource': true,
		'markdownit': true,
		'TurndownService': true,
		'html2pdf': true,
		'Prism': true,
		'refreshFsLightbox': true,
		'gsap': true,
		'SplitText': true,
		'ScrollSmoother': true,
		'ScrollTrigger': true,
		'Observer': true,
		'markdownItKatex': true,
		'diffDOM': true,
		'Konva': true,
		'ColorPicker': true,
		'tinycolor': true,
	},
	rules: {
		'indent': [
			'warn',
			'tab',
			{ 'SwitchCase': 1 }
		],
		'linebreak-style': [
			'warn',
			'unix'
		],
		'quotes': [
			'warn',
			'single'
		],
		'semi': [
			'warn',
			'always'
		],
		'object-curly-spacing': [
			'warn',
			'always'
		],
		'array-bracket-spacing': [
			'warn',
			'always'
		],
		'key-spacing': [
			'warn', { 'afterColon': true, 'mode': 'strict' }
		],
		'comma-spacing': [
			'warn', { 'before': false, 'after': true }
		],
		'arrow-parens': [
			'warn', 'as-needed'
		],
		'keyword-spacing': [
			'warn', { 'before': true, 'after': true }
		],
		'@typescript-eslint/no-explicit-any': 'off',
		// 'readable-tailwind/multiline': [
		// 	'warn',
		// 	{
		// 		'indent': 'tab'
		// 	}
		// ]
	}
};
