const textHighlight = require('../text-highlight.js')
const { respond_comment, post_comment } = require('../comments.js')

const comment_forms = document.querySelectorAll( '.comment_form' )
for ( const comment_form of comment_forms ) {
  comment_form.onsubmit = respond_comment
}

const article_element    = document.querySelector( 'main' )
const selection_toolbar  = document.querySelector( '.toolbar' )
const textRange = textHighlight(article_element, selection_toolbar)
