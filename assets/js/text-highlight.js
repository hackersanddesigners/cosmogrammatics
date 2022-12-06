"use strict";
const Highlighter = require('web-highlighter')

function textHighlight() {
  const highlighter = new Highlighter();

  highlighter.on(Highlighter.event.CREATE, ({sources}) => {
    console.log(sources);
  });

  highlighter.run();
}

module.exports = textHighlight;
