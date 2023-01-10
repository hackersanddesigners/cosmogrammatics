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

  // remove every node inside comment-data
  Array.from(comment_data.childNodes).map(item => {
    item.remove()
  })

  
  // populate comment-data w/ unpublished comments from local-storage
  const comment_store = new LocalStore(`comment-${article_slug}`)
  const comments = comment_store.getAll()

  const status = document.querySelector('.comment-status')
  status.innerHTML = `You have ${comments.length} unpublished comments.`

  comments.map((comment, idx) => {
    const el = make_comment_el(comment, idx, article_slug)
    comment_data.append(el)
  })

  const publish = document.querySelector('.post_comment')
  publish.addEventListener('click', () => {
    // remove all items from comments store
    comment_store.removeAll()
  })
  
}

function make_comment_el(comment, idx, article_slug) {

  const wrapper = document.createElement('div')
  wrapper.classList.add('comment-list-wrapper')
  wrapper.setAttribute('id', comment.content.selection_text.id)

  const comment_input = document.createElement('input')
  comment_input.setAttribute('type', 'checkbox')
  comment_input.setAttribute('id', '')
  comment_input.setAttribute('name', '')
  comment_input.setAttribute('checked', 'checked')
  comment_input.classList.add('comment-list-input')

  const comment_label = document.createElement('label')
  comment_label.setAttribute('for', '<id>')
  comment_label.classList.add('comment-list-label')

  // text
  const comment_text = document.createElement('p')
  comment_text.append(comment.content.text)

  // date
  const comment_date = document.createElement('p')
  const timestamp = document.createElement('time')
  timestamp.setAttribute('datetime', comment.content.timestamp)

  comment_date.append(timestamp)
  comment_date.innerHTML = `on ${comment.content.timestamp}` 

  const comment_obj_data = document.createElement('input')
  comment_obj_data.setAttribute('type', 'hidden')
  comment_obj_data.setAttribute('id', `comment_data[]`)
  comment_obj_data.setAttribute('name', `comment_data[]`)
  comment_obj_data.setAttribute('value', JSON.stringify(comment))

  const comment_remove = document.createElement('button')
  comment_remove.setAttribute('type', 'button')
  comment_remove.innerHTML = 'Remove'
  comment_remove.classList.add('comment-list-remove')

  const comment_store = new LocalStore(`comment-${article_slug}`)
  comment_remove.addEventListener('click', () => {
    comment_store.remove(comment.content.selection_text.id)
    commentReviewList(article_slug)      
  })

  const comment_label_wrapper = document.createElement('div')
  comment_label_wrapper.append(comment_text)
  comment_label_wrapper.append(comment_date)
  comment_label_wrapper.append(comment_obj_data)

  comment_label.append(comment_label_wrapper)
  comment_label.append(comment_remove)

  wrapper.append(comment_input)
  wrapper.append(comment_label)


  return wrapper

}

module.exports = { commentReviewToggle, commentReviewList }
