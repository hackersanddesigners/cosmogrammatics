const fetchArticleHighlightsFromAPI = require('../fetch-article-highlights-from-api')
const textHighlight = require('../text-highlight')
const { respond_comment, commentsArticle } = require('../comments')
const LocalStore = require('../local.store')
const {commentReviewToggle, commentReviewList} = require('../comment-review.js')


const article_slug = window.location.pathname.split('/').pop().split('/').join('+')
const comment_forms = document.querySelectorAll( '.comment_form' )

const user_store = new LocalStore('user')
const user = user_store.getByID(article_slug)

const comment_store = new LocalStore(`comment-${article_slug}`)
const comments = comment_store.getAll()

for (const comment_form of comment_forms) {
  // -- set comment to default username if there's any
  if (user !== undefined) {
    comment_form.querySelector('#author').value = user.value
  }

  // -- setup article's comments
  const blockID = comment_form.dataset.blockId
  const comment = comments.find(comment => comment.content.block_id === blockID)

  if (comment !== undefined) {
    commentsArticle(comment, comment_form)
  }

  comment_form.onsubmit = respond_comment
}

// synchronize text-highlights backend data with localStorage

;(async() => {
  await fetchArticleHighlightsFromAPI(article_slug)
})()

const article_element    = document.querySelector( 'main' )
const selection_toolbar  = document.querySelector( '.toolbar' )
const textRange = textHighlight(article_element, selection_toolbar, article_slug)


// comment-review list
commentReviewToggle()
commentReviewList(article_slug)
