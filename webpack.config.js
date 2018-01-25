const path = require('path');
const ExtractTextPlugin = require('extract-text-webpack-plugin');

module.exports = {
  entry: ['./app/resources/js/app.js', './app/resources/css/app.scss'],
  output: {
    path: path.resolve(__dirname, './public/assets'),
    filename: 'app.js'
  },
  module: {
    rules: [
      {
        test: /\.scss$/,
        use: ExtractTextPlugin.extract({
          fallback: 'style-loader',
          use: ['css-loader', 'sass-loader']
        })
      },
      {
        test: /.(ttf|otf|eot|svg|woff(2)?)(\?[a-z0-9]+)?$/,
        use: [{
          loader: 'file-loader',
          options: {
            name: '[name].[ext]',
            outputPath: 'fonts/',    // where the fonts will go
            //publicPath: '/assets'       // override the default path
          }
        }]
      }
    ]
  },
  plugins: [
    new ExtractTextPlugin('app.css')
  ]
};