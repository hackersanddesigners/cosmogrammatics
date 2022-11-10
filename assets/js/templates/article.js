import {
  $,
  $$,
  SelectionObserver
} from '../utils/index.js'

import {
  respond_comment
} from '../comments.js'


const article_element    = $( 'main' )
const comment_forms      = $$( '.comment_form' )
const selection_observer = new SelectionObserver( article_element )

for ( const comment_form of comment_forms ) {
  comment_form.onsubmit = respond_comment
}
