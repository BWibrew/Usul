module.exports = {
    "env": {
        "browser": true,
        "es6": true,
        "commonjs": true
    },
    "extends": [
        "eslint:recommended",
        "plugin:vue/recommended",
    ],
    "parserOptions": {
        "sourceType": "module"
    },
    "rules": {
        "linebreak-style": [
            "error",
            "unix"
        ],
        "quotes": [
            "error",
            "single"
        ],
        "semi": [
            "error",
            "never"
        ]
    },
    "globals": {
        "Vue": true
    }
};
