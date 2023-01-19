const fetchArticleHighlightsFromAPI = require('../fetch-article-highlights-from-api')
const textHighlight = require('../text-highlight')
const { respond_comment, commentsArticle, blockFocus } = require('../comments')
const LocalStore = require('../local.store')
const {commentReviewToggle, commentReviewList} = require('../comment-review.js')


const article_slug = window.location.pathname.split('/').pop().split('/').join('+')

// -- synchronize text-highlights backend data with localStorage
;(async() => {
  await fetchArticleHighlightsFromAPI(article_slug)
})()

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

  // -- setup article's draft comments
  //    check if comment has `status: draft` 
  //    and matches current block-id
  const blockID = comment_form.dataset.blockId
  const comments_draft = comments.filter(comment => {
    if (comment.status === 'draft' && comment.content.block_id === blockID) {
      return comment
    }
  })

  if (comments_draft.length > 0) {
    comments_draft.map(comment => {
      commentsArticle(comment, comment_form)
    })
  }

  comment_form.onsubmit = respond_comment
}

const article_element = document.querySelector( 'main' )
const selection_toolbar = document.querySelector( '.toolbar' )
const textRange = textHighlight(article_element, selection_toolbar, article_slug)

// comment-review list
commentReviewToggle()
commentReviewList(article_slug)

blockFocus()
