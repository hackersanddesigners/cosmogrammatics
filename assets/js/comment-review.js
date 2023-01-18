const LocalStore = require('./local.store')
const store = new LocalStore()

// -- comment-review
//    display unpublished comments
//    saved in localStorage,
//    allow to remove unwanted ones,
//    edit them, and then selectively
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
  let comments = comment_store.getAll() 

  // remove published comments from list
  // we display in comment-review only draft comments 
  comments = comments.filter(comment => comment.status === 'draft')

  comments.map((comment, idx) => {
    const el = make_comment_el(comment, idx, article_slug)
    comment_data.append(el)
  })

  // -- setup comment status and username
  const status = document.querySelector('.comment-status')
  status.innerHTML = `You have ${comments.length} unpublished comments.`

  // -- save username into a different, own store
  //    so we can more easily fetch it and pre-add it
  //    to any other comment done afterwards
  let user_name = ''
  if (comments.length > 0) {
    user_name = comments[0].content.user
  }

  const username = setUsername(user_name, article_slug)

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
  //    & remove all comments
  const publish = document.querySelector('.post_comment')
  const remove_all = document.querySelector('.comment-remove-all')

  if (comments.length > 0) {
    // -- publish selected comments
    publish.removeAttribute('disabled')

    publish.addEventListener('click', () => {
      const inputs = Array.from(document.querySelectorAll('.comment-list-input'))

      // remove all input-checked items from comments store
      inputs.map(input => {
        if (input.checked) {
          const highlight_id = input.parentNode.id
          comment_store.remove(highlight_id)
        }
      })
    })
 
    // -- remove all comments
    remove_all.removeAttribute('disabled')

    remove_all.addEventListener('click', () => {
      comments.map(comment => {
        const highlight_id = comment.content.selection_text.id
        removeCommentDOM(highlight_id)
      })

      comment_store.removeAll()
      commentReviewList(article_slug)
    })

    updateCommentCounter('decrease', comments.length)

  } else {
    publish.setAttribute('disabled', 'disabled')
    remove_all.setAttribute('disabled', 'disabled')
  }
  
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
  comment_input.setAttribute('id', `comment_data[]`)
  comment_input.setAttribute('name', `comment_data[]`)
  comment_input.classList.add('comment-list-input')

  // set / remove data from input-checkbox field
  // whenever the checkbox is clicked
  comment_input.addEventListener('click', (e) => {
    const checkbox = e.target

    if (checkbox.checked) {
      // set comment to published
      // add data to input-checkbox
      comment['status'] = 'published'
      checkbox.value = JSON.stringify(comment)

    } else {
      // put back comment to draft
      // reset data from input-checkbox
      comment['status'] = 'draft'
      checkbox.value = ''
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

  const highlight_id = comment.content.selection_text.id

  comment_remove.addEventListener('click', () => {
    removeCommentDOM(highlight_id)
    comment_store.remove(highlight_id)
    commentReviewList(article_slug)
  })

  // -- add link to text-selection span
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

// save username into a different, own store
// so we can more easily fetch it and pre-add it
// to any other comment done afterwards
function setUsername(name, article_slug) {

  const user_store = new LocalStore('user')
  const user = user_store.getByID(article_slug)

  let username = {id: '', value: ''}

  if (user === undefined || user.value === '') {
    username['id'] = `${article_slug}`
    username['value'] = name

    user_store.save(username)

  } else {
    username = user
  }

  return username

}

function removeCommentDOM(highlight_id) {
  // remove text-highlight
  const tip = document.querySelector(`[data-highlight-id="${highlight_id}"]`)
  if (tip !== null) {
    tip.remove()
  }

  const article_comment = document.querySelector(`[data-text-selection-id="${highlight_id}"]`)
  if (article_comment !== null) {
    article_comment.remove()
  }

}

function updateCommentCounter(op, amount) {
  // update comment count
  const comment_count = document.querySelector('#comment_count')
  const current_count = new Number(comment_count.innerHTML)

  if (op === 'increase') {
    comment_count.innerHTML = current_count + amount

  } else if (op === 'decrease') {
    const result = current_count - amount
    comment_count.innerHTML = result < 0 ? 0 : result
  }

}

module.exports = { commentReviewToggle,
                   commentReviewList,
                   setUsername,
                   updateCommentCounter }
