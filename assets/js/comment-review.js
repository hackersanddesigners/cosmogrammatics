const LocalStore = require('./local.store')
const store = new LocalStore()

// -- comment-review
//    display unpublished comments
//    saved in localStorage,
//    allow to remove unwanted ones
//    (maybe edit even?) and then
//    publish them all in one go


function commentReviewToggle() {
  const comment_toggle = document.querySelector('.comment-toggle')
  const comment_list = document.querySelector('.comment-list')

  comment_toggle.addEventListener('click', () => {
    comment_list.classList.toggle('hidden')
  })
}

function commentReviewList(article_slug) {

  const comment_data = document.querySelector('.comment-data')

  // populate comment-data w/ unpublished comments from local-storage
  const comment_store = new LocalStore(`comment-${article_slug}`)
  const comments = comment_store.getAll()

  const status = document.createElement('p')
  status.innerHTML = `You have ${comments.length} unpublished comments.`
  comment_data.append(status)

  comments.map(comment => {
    make_comment_el(comment)
  })

  function make_comment_el(comment) {
    const wrapper = document.createElement('div')
    wrapper.classList.add('comment-list-wrapper')

    const comment_input = document.createElement('input')
    comment_input.setAttribute('type', 'checkbox')
    comment_input.setAttribute('id', '')
    comment_input.setAttribute('name', '')
    comment_input.setAttribute('checked', 'checked')
    comment_input.classList.add('comment-list-input')

    const comment_label = document.createElement('label')
    comment_label.setAttribute('for', '<id>')
    comment_label.classList.add('comment-list-label')

    const comment_text = document.createElement('p')
    comment_text.append(comment.content.text)

    const date = document.createElement('p')
    const timestamp = document.createElement('time')
    timestamp.setAttribute('datetime', comment.content.timestamp)

    date.append(timestamp)
    date.innerHTML = `on ${comment.content.timestamp}`

    // const user = document.createElement('p')
    // user.innerHTML = `by ${comment.content.user}`

    const comment_label_wrapper = document.createElement('div')
    comment_label_wrapper.append(comment_text)
    comment_label_wrapper.append(date)

    comment_label.append(comment_label_wrapper)

    wrapper.append(comment_input)
    wrapper.append(comment_label)


    // -- append to comment-list
    comment_data.append(wrapper)

  }

}

module.exports = { commentReviewToggle, commentReviewList }
