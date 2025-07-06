module.exports = {
    extends: [
        "stylelint-config-standard",
        "stylelint-config-tailwindcss"
    ],
    plugins: [],
    rules: {
        // "comment-empty-line-before": null,
        "at-rule-no-unknown": [
            true,
            {
                ignoreAtRules: [
                    "tailwind",
                    "apply",
                    "layer",
                    "variants",
                    "responsive",
                    "screen",
                    "plugin",
                    "custom-variant",
                    "variant",
                    "source",
                    "themes"
                ]
            }
        ],
        "function-no-unknown": [
            true,
            {
                ignoreFunctions: ["theme", "oklch"]
            }
        ],
        "declaration-property-value-no-unknown": true,
        "property-no-unknown": [
            true,
            {
                ignoreProperties: ["--*"]
            }
        ],
        "hue-degree-notation": null
    }
};
