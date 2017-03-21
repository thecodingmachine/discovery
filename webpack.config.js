let libraryName = "TCM-couscous-theme";
let path = require("path");
let webpack = require("webpack");

module.exports = {
    entry: path.resolve(__dirname, './app.js'),
    output: {
        path: './build/',
        publicPath: "./",
        filename: 'app.bundle.js',
        //library: libraryName,
        //libraryTarget: 'umd',
        //umdNamedDefine: true
    },
    devtool: 'source-map',
    module: {
        loaders: [
            { test: /\.css$/, loader: 'style-loader!css-loader' },
            { test: /\.less$/, loader: 'style-loader!css-loader!less-loader' },
            { test: /\.png$/, loader: "url-loader?limit=100000",
                query: {
                    name: 'images/[name].[ext]',
                    publicPath: '/build/'
                }},
            { test: /\.jpg$/, loader: "file-loader",
                query: {
                    name: 'images/[name].[ext]',
                    publicPath: '/build/'
                }},
            { test: /\.gif$/, loader: "file-loader",
                query: {
                    name: 'images/[name].[ext]',
                    publicPath: '/build/'
                }},
            { test: /\.woff(2)?(\?v=[0-9]\.[0-9]\.[0-9])?$/, loader: "url-loader",
                query: {
                    limit: 10000,
                    mimetype: 'application/font-woff',
                    name: 'fonts/[name].[ext]',
                    publicPath: '/build/'
                }},
            { test: /\.(ttf|eot|svg)(\?v=[0-9]\.[0-9]\.[0-9])?$/, loader: "file-loader",
                query: {
                    limit: 10000,
                    name: 'fonts/[name].[ext]',
                    publicPath: '/build/'
                }},

            /*{ test: /\.(woff2?|ttf|eot|svg)$/, loader: 'url-loader?limit=10000' },*/
            { test: /\.js$/, loader: "babel-loader", query: {cacheDirectory: true, presets: [__dirname+"/node_modules/babel-preset-es2015"]}}
        ]
    },
    plugins: [
        // Hack to not bundle all languages from highlight.js to reduce bundle size.
        // See: https://bjacobel.com/2016/12/04/highlight-bundle-size/
        new webpack.ContextReplacementPlugin(
            /highlight\.js\/lib\/languages$/,
            new RegExp(`^./(${['javascript', 'php', 'bash', 'sql', 'css', 'less', 'json'].join('|')})$`)
)
        /*new webpack.ProvidePlugin({
            hljs: "jquery",
            jQuery: "jquery"
        })*/
        //new webpack.optimize.CommonsChunkPlugin("init.js"),
        // Extracts the CSS in CSS files (otherwise it would be bundled in the JS!)
        //new ExtractTextPlugin("[name].css")
    ],
    resolve: {
        alias: {
            "hljs": path.join(__dirname, "node_modules/highlightjs/highlight.pack")
        //    'vue$': 'vue/dist/vue.common.js',
        //    '_components':  path.resolve(__dirname, './membership/src/components')
        }
    }//,
    //vue: {}
};