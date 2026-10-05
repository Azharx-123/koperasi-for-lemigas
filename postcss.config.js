import purgecss from '@fullhuman/postcss-purgecss';

// PurgeCSS scans these files for class names actually used, then strips
// everything else out of the compiled Bootstrap + Font Awesome + custom
// CSS. The safelist below exists because several classes are only ever
// added at runtime by JS (Bootstrap's collapse/dropdown/modal/carousel,
// and AOS's scroll-triggered classes) — they never appear as static text
// in a .blade.php file, so PurgeCSS would otherwise remove them and break
// those interactions.
export default {
    plugins: [
        purgecss({
            content: [
                './resources/**/*.blade.php',
                './resources/**/*.js',
                './app/**/*.php',
            ],
            defaultExtractor: (content) => content.match(/[\w-/:%.]+(?<!:)/g) || [],
            safelist: {
                standard: [
                    /^show$/, /^showing$/, /^hiding$/, /^fade$/, /^collapsing$/,
                    /^collapse$/, /^collapsed$/,
                    /^disabled$/, /^active$/,
                ],
                deep: [
                    /^carousel/, /^dropdown/, /^dropstart/, /^dropend/, /^dropup/,
                    /^modal/, /^alert/, /^aos-/, /^fa-/, /^fas$/, /^far$/, /^fab$/,
                ],
            },
        }),
    ],
};
