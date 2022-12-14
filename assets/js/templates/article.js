const fetchArticleHighlightsFromAPI = require('../fetch-article-highlights-from-API')
const textHighlight = require('../text-highlight')
const { respond_comment, post_comment } = require('../comments')

const comment_forms = document.querySelectorAll( '.comment_form' )
for ( const comment_form of comment_forms ) {
  comment_form.onsubmit = respond_comment
}

// synchronize text-highlights backend data with localStorage
const article_slug = window.location.pathname.split('/').pop().split('/').join('+')

;(async() => {
  await fetchArticleHighlightsFromAPI(article_slug)
})()

const article_element    = document.querySelector( 'main' )
const selection_toolbar  = document.querySelector( '.toolbar' )
const textRange = textHighlight(article_element, selection_toolbar)
