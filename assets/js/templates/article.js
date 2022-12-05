import {
  $,
  $$,
  SelectionObserver
} from '../utils/index.js'

import {
  respond_comment,
  post_comment
} from '../comments.js'

// import { textHighlight } from '../text-highlight.min.js';
// const textHighlight = require('../text-highlight.js');
import textHighlight from '../text-highlight.js';
// const textHighlight = require('../text-highlight-bundle.js')
// import { textHighlight } from '../text-highlight-bundle.js';


const comment_forms      = $$( '.comment_form' )

for ( const comment_form of comment_forms ) {
  comment_form.onsubmit = respond_comment
}


const article_element    = $( 'main' )
const selection_toolbar  = $( '.toolbar' )
// const selection_observer = new SelectionObserver( article_element, selection_toolbar )

