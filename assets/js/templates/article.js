const { respond_comment, post_comment } = require('../comments.js')

const comment_forms = document.querySelectorAll( '.comment_form' )
for ( const comment_form of comment_forms ) {
  comment_form.onsubmit = respond_comment
}


// const article_element    = document.querySelector( 'main' )
// const selection_toolbar  = document.querySelector( '.toolbar' )
// const selection_observer = new SelectionObserver( article_element, selection_toolbar )

const textHighlight = require('../text-highlight.js')
// should i pass anything back into this
// maybe the comments selection offset?
textHighlight();
