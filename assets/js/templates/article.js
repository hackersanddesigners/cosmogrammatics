const fetchArticleHighlightsFromAPI = require('../fetch-article-highlights-from-api')
const textHighlight = require('../text-highlight')
const { respond_comment } = require('../comments')
const LocalStore = require('../local.store')
const {commentReviewToggle, commentReviewList} = require('../comment-review.js')


const article_slug = window.location.pathname.split('/').pop().split('/').join('+')
const comment_forms = document.querySelectorAll( '.comment_form' )

const user_store = new LocalStore('user')
const user = user_store.getByID(article_slug)

for (const comment_form of comment_forms) {
  if (user !== undefined) {
    comment_form.querySelector('#author').value = user.value
  }

  comment_form.onsubmit = respond_comment
}

// synchronize text-highlights backend data with localStorage

;(async() => {
  await fetchArticleHighlightsFromAPI(article_slug)
})()

const article_element    = document.querySelector( 'main' )
const selection_toolbar  = document.querySelector( '.toolbar' )
const textRange = textHighlight(article_element, selection_toolbar)


// comment-review list
commentReviewToggle()
commentReviewList(article_slug)
