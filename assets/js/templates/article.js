const textHighlight = require('../text-highlight')
const { respond_comment, commentsArticle, blockFocus, comments } = require('../comments')
const LocalStore = require('../local.store')
const { commentReviewList } = require('../comment-review')
const viewMode = require('../view-mode')
const footnotesDesign = require('../footnotes-design')

;(async() => {
  
  const article_slug = window.location.pathname.split('/').pop().split('/').join('+')

  // text-highlight
  const article_element = document.querySelector( 'main' )
  const selection_toolbar = document.querySelector( '.toolbar' )

  await textHighlight(article_element, selection_toolbar, article_slug)

  // comments
  comments(article_slug)

  // view-mode
  viewMode()

  // comment-review list
  commentReviewList(article_slug)

  blockFocus()

  // footnotes design
  footnotesDesign()

})()
