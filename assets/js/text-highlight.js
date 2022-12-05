// "use strict";
// const Highlighter = require('web-highlighter')

import { Highlighter } from 'web-highlighter';
// import * as Highlighter from 'web-highlighter';

// function textHighlight() {
export default function textHighlight() {
  const highlighter = new Highlighter();

  highlighter.on(Highlighter.event.CREATE, ({sources}) => {
    console.log(sources);
  });

  highlighter.run();
}

// textHighlight();
// module.exports = textHighlight;
