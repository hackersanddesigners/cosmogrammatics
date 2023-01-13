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

  // -- init, view reset
  const comment_data = document.querySelector('.comment-data')

  // remove every node inside comment-data
  Array.from(comment_data.childNodes).map(item => {
    item.remove()
  })
 
  // -- populate comment-data w/ unpublished comments from local-storage
  const comment_store = new LocalStore(`comment-${article_slug}`)
  const comments = comment_store.getAll() 

  comments.map((comment, idx) => {
    const el = make_comment_el(comment, idx, article_slug)
    comment_data.append(el)
  })

  // -- setup comment status and username
  const status = document.querySelector('.comment-status')
  status.innerHTML = `You have ${comments.length} unpublished comments.`

  // assume first comment done on the page sets the username
  // for the time being (until username is explicitly edited)
  const username_value = comments[0].content.user
  console.log('username_value =>', username_value)

  const username_edit = document.querySelector('.comment-username-wrapper')
  const username_input = username_edit.querySelector('input')
  username_input.value = username_value
  // set input text width to specific username length
  username_input.style.width = `${username_input.value.length +1}ch`

  const username_edit_btn = document.querySelector('.comment-username-edit-btn')
  username_edit_btn.addEventListener('click', (e) => {
    const target = e.target
    
    if (target.textContent === 'Save') {
      // save input text
      // => replace all comment.content.user w/ value in input text?
      comments.map(comment => {
        comment.content.user = username_input.value
        comment_store.save(comment)
      })

      // reset styles
      target.textContent = 'Save'

      username_input.setAttribute('readonly', 'readonly')
      username_input.style.width = `${username_input.value.length +1}ch`
      username_input.style.border = 'none'

      document.querySelector('body').focus()

    } else if (target.textContent === 'Edit') {
      // toggle styles

      target.textContent = 'Save'
      
      username_input.removeAttribute('readonly')
      username_input.style.width = 'auto'
      username_input.style.border = 'auto'

      username_input.focus()
    }
  })

  // -- edit comment text
  

  // -- publish selected comments
  const publish = document.querySelector('.post_comment')
  publish.addEventListener('click', () => {
    // if comments are 0, disable button or display message?

    // remove all items from comments store
    comment_store.removeAll()
  })
  
}

function make_comment_el(comment, idx, article_slug) {

  // -- wrapper node
  const wrapper = document.createElement('div')
  wrapper.classList.add('comment-list-wrapper')
  wrapper.setAttribute('id', comment.content.selection_text.id)

  // -- input checkbox
  const comment_input = document.createElement('input')
  comment_input.setAttribute('type', 'checkbox')
  comment_input.setAttribute('id', '')
  comment_input.setAttribute('name', '')
  comment_input.setAttribute('checked', 'checked')
  comment_input.classList.add('comment-list-input')

  // -- label
  const comment_label = document.createElement('label')
  comment_label.setAttribute('for', '<id>')
  comment_label.classList.add('comment-list-label')

  // -- text
  const comment_text = document.createElement('p')
  comment_text.append(comment.content.text)

  // -- date
  const comment_date = document.createElement('p')
  const timestamp = document.createElement('time')
  timestamp.setAttribute('datetime', comment.content.timestamp)

  comment_date.append(timestamp)
  comment_date.innerHTML = `on ${comment.content.timestamp}` 

  // -- hidden input to send data via POST request
  const comment_obj_data = document.createElement('input')
  comment_obj_data.setAttribute('type', 'hidden')
  comment_obj_data.setAttribute('id', `comment_data[]`)
  comment_obj_data.setAttribute('name', `comment_data[]`)
  comment_obj_data.setAttribute('value', JSON.stringify(comment))

  // -- comment remove button
  const comment_remove = document.createElement('button')
  comment_remove.setAttribute('type', 'button')
  comment_remove.innerHTML = 'Remove'
  comment_remove.classList.add('comment-list-remove')

  const comment_store = new LocalStore(`comment-${article_slug}`)
  comment_remove.addEventListener('click', () => {
    comment_store.remove(comment.content.selection_text.id)
    commentReviewList(article_slug)      
  })

  // -- add link to text-selection span
  const highlight_id = comment.content.selection_text.id
  const target = document.querySelector(`[data-highlight-id="${highlight_id}"]`)
  target.setAttribute('id', highlight_id) 

  const show_text_selection = document.createElement('a')
  show_text_selection.setAttribute('href', `#${highlight_id}`)
  show_text_selection.innerHTML = 'Show Text Selection'
  show_text_selection.classList.add('comment-list-show-highlight')

  show_text_selection.addEventListener('click', (e) => {
    // set border around text-selection for a
    // specific amount of time, then reset the style

    target.style.border = '1px solid blue'

    setTimeout(() => {
      target.style.border = ''
    }, '3000')
  })

  // --

  // -- append above nodes to label wrapper node
  const comment_label_wrapper = document.createElement('div')
  comment_label_wrapper.classList.add('comment-label-wrapper')
  comment_label_wrapper.append(comment_text)
  comment_label_wrapper.append(comment_date)
  comment_label_wrapper.append(comment_obj_data)

  // -- append wrapper nodes to label node
  comment_label.append(comment_label_wrapper)
  comment_label.append(show_text_selection)
  comment_label.append(comment_remove)

  // -- append input and label nodes to root wrapper node
  wrapper.append(comment_input)
  wrapper.append(comment_label)


  return wrapper

}

module.exports = { commentReviewToggle, commentReviewList }
