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

  // save username into a different, own store
  // so we can more easily fetch it and pre-add it
  // to any other comment done afterwards
  const user_store = new LocalStore('user')
  const user = user_store.getByID(article_slug)

  let username = {id: '', value: ''}
  if (user === undefined) {
    username['id'] = `${article_slug}`

    if (comments.length > 0) {
      username['value'] = comments[0].content.user
    }

    user_store.save(username)

  } else {
    username = user
  }

  // -- set username to comment-list-status area
  const username_edit = document.querySelector('.comment-username-wrapper')
  const username_input = username_edit.querySelector('input')
  username_input.value = username.value

  // set input text width to specific username length
  username_input.style.width = `${username_input.value.length +1}ch`
  // --

  const username_edit_btn = document.querySelector('.comment-username-edit-btn')
  username_edit_btn.addEventListener('click', (e) => {
    const target = e.target
    
    if (target.textContent === 'Save') {
      // save input text
      username.value = username_input.value
      user_store.save(username)

      // reset styles
      target.textContent = 'Edit'

      username_input.setAttribute('readonly', 'readonly')
      username_input.style.width = `${username_input.value.length +1}ch`
      username_input.style.border = 'none'

      document.querySelector('body').focus()

    } else if (target.textContent === 'Edit') {
      // toggle styles

      target.textContent = 'Save'

      moveCaretToEnd(username_input)
      
      username_input.removeAttribute('readonly')
      username_input.style.width = 'auto'
      username_input.style.border = 'auto'

      username_input.focus()
    }

  })

  // -- publish selected comments
  const publish = document.querySelector('.post_comment')

  if (comments.length > 0) {
    publish.removeAttribute('disabled')

    // remove all input-checked items from comments store
    publish.addEventListener('click', (e) => {
      const inputs = Array.from(document.querySelectorAll('.comment-list-input'))

      inputs.forEach(input => {
        if (input.checked) {
          const comment_id = input.parentNode.id
          console.log('comment_id =>', comment_id)
          comment_store.remove(comment_id)
        }
      })

    })

  } else {
    publish.setAttribute('disabled', 'disabled')
  }

  // -- remove all comments
  const remove_all = document.querySelector('.comment-remove-all')
  remove_all.addEventListener('click', () => {
    comments.map(comment => {
      const comment_id = comment.id

      const tip = document.querySelector(`[data-id="${comment_id}"]`)
      if (tip !== null) {
        tip.remove()
      }
    })

    // remove all comments
    comment_store.removeAll()

  })
  
}

function make_comment_el(comment, idx, article_slug) {
  const comment_store = new LocalStore(`comment-${article_slug}`)

  // -- wrapper node
  const wrapper = document.createElement('div')
  wrapper.classList.add('comment-list-wrapper')
  wrapper.setAttribute('id', comment.content.selection_text.id)

  // -- input checkbox
  const comment_input = document.createElement('input')
  comment_input.setAttribute('type', 'checkbox')
  comment_input.setAttribute('id', `comment-list-${idx}`)
  comment_input.setAttribute('name', `comment-list`)
  comment_input.classList.add('comment-list-input')

  comment_input.addEventListener('click', (e) => {
    // create / remove input hidden field
    // whenever the checkbox is clicked

    console.log('comment-input =>', e.target)
    const checkbox = e.target

    if (checkbox.checked) {
      // add input-hidden field
      const comment_obj_data = document.createElement('input')
      comment_obj_data.setAttribute('type', 'hidden')
      comment_obj_data.setAttribute('id', `comment_data[]`)
      comment_obj_data.setAttribute('name', `comment_data[]`)
      comment_obj_data.setAttribute('value', JSON.stringify(comment))

      comment_label_wrapper.append(comment_obj_data)

    } else {
      // remove input-hidden field
      const input_hidden = comment_label_wrapper.querySelector('input[type=hidden]')
      input_hidden.remove()

    }
  })

  // -- label
  const comment_label = document.createElement('label')
  comment_label.setAttribute('for', `comment-list-${idx}`)
  comment_label.classList.add('comment-list-label')

  // -- text
  const comment_input_text = document.createElement('input')
  comment_input_text.setAttribute('type', 'text')
  comment_input_text.setAttribute('id', `comment-list-text-${idx}`)
  comment_input_text.setAttribute('text', 'comment-list-text')
  comment_input_text.setAttribute('value', comment.content.text)
  comment_input_text.setAttribute('readonly', 'readonly')


  // -- date
  const comment_date = document.createElement('p')
  const timestamp = document.createElement('time')
  timestamp.setAttribute('datetime', comment.content.timestamp)

  comment_date.append(timestamp)
  comment_date.innerHTML = `on ${comment.content.timestamp}` 
 
  // -- comment edit button
  const comment_edit = document.createElement('button')
  comment_edit.setAttribute('type', 'button')
  comment_edit.innerHTML = 'Edit'
  comment_edit.classList.add('comment-list-edit')

  comment_edit.addEventListener('click', (e) => {
    const target = e.target

    if (target.textContent === 'Save') {
      // save input text
      comment.content.text = comment_input_text.value
      comment_store.save(comment)

      // reset styles
      target.textContent = 'Edit'

      comment_input_text.setAttribute('readonly', 'readonly')
      comment_input_text.style.width = `${comment_input_text.value.length +1}ch`
      comment_input_text.style.border = 'none'

      document.querySelector('body').focus()

    } else if (target.textContent === 'Edit') {
      // toggle styles

      target.textContent = 'Save'

      moveCaretToEnd(comment_input_text)
      
      comment_input_text.removeAttribute('readonly')
      comment_input_text.style.width = 'auto'
      comment_input_text.style.border = 'auto'

      comment_input_text.focus()
    }

  })

  // -- comment remove button
  const comment_remove = document.createElement('button')
  comment_remove.setAttribute('type', 'button')
  comment_remove.innerHTML = 'Remove'
  comment_remove.classList.add('comment-list-remove')

  comment_remove.addEventListener('click', () => {
    comment_store.remove(comment.content.selection_text.id)
    commentReviewList(article_slug)      
  })

  // -- add link to text-selection span
  const highlight_id = comment.content.selection_text.id
  const target = document.querySelector(`[data-highlight-id="${highlight_id}"]`)
  if (target !== null) {
    target.setAttribute('id', highlight_id) 
  }

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
  comment_label_wrapper.append(comment_input_text)
  comment_label_wrapper.append(comment_date)

  // -- append wrapper nodes to label node
  comment_label.append(comment_label_wrapper)
  comment_label.append(comment_edit)
  comment_label.append(comment_remove)
  comment_label.append(show_text_selection)

  // -- append input and label nodes to root wrapper node
  wrapper.append(comment_input)
  wrapper.append(comment_label)


  return wrapper

}


// <https://stackoverflow.com/a/4716021>
function moveCaretToEnd(el) {
  if (typeof el.selectionStart == "number") {
    el.selectionStart = el.selectionEnd = el.value.length;
  } else if (typeof el.createTextRange != "undefined") {
    el.focus();
    var range = el.createTextRange();
    range.collapse(false);
    range.select();
  }
}

module.exports = { commentReviewToggle, commentReviewList }
