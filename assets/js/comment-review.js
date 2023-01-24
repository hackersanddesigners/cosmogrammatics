const LocalStore = require('./local.store')
const store = new LocalStore()

// -- comment-review
//    display unpublished comments
//    saved in localStorage,
//    allow to remove unwanted ones,
//    edit them, and then selectively
//    publish them all in one go

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
  if (comments.length ) {
    const comment_status = document.querySelector('.comment-status')
    comment_status.innerHTML = 'Your unpublished comments:'
  }

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

  const user_store = new LocalStore('user')
  const username_edit_btn = document.querySelector('.comment-username-edit-btn')

  username_edit_btn.addEventListener('click', (e) => {
    const target = e.target
    
    if (target.textContent === 'save') {
      // save input text
      username.value = username_input.value
      user_store.save(username)

      // reset styles
      target.textContent = 'edit'

      username_input.setAttribute('readonly', 'readonly')
      username_input.style.width = `${username_input.value.length +1}ch`
      username_input.style.borderColor = 'transparent'

      document.querySelector('body').focus()

    } else if (target.textContent === 'edit') {
      // toggle styles

      target.textContent = 'save'

      moveCaretToEnd(username_input)
      
      username_input.removeAttribute('readonly')
      username_input.style.width = 'auto'
      username_input.style.borderColor = 'black'

      username_input.focus()
    }

  })

  // -- publish, edit and remove selected comments
  const publish_btn = document.querySelector('.post_comment')
  const edit_btn = document.querySelector('.comment-edit')
  const remove_btn = document.querySelector('.comment-remove')

  if (comments.length > 0) {
    const inputs = Array.from(document.querySelectorAll('.comment-list-input'))

    // -- publish selected comments
    publish_btn.classList.remove('hidden')

    publish_btn.addEventListener('click', (e) => {

      inputs.map(input => {
        
        if (input.checked) {
          const highlight_id = input.parentNode.id
          const comment = comments.find(comment => {
            if (comment.content.selection_text !== undefined) {
              if (comment.content.selection_text.id === highlight_id) {
                return comment
              }
            } else {
              if (comment.content.block_id === highlight_id) {
                return comment
              }
            }
          })

          comment['status'] = 'published'
          input.value = JSON.stringify(comment)

          // remove selected input-checked items from comments store
          comment_store.remove(highlight_id)

        } else {
          input.value = ''

        }

      })

    })

    // -- edit selected comments
    edit_btn.classList.remove('hidden')

    edit_btn.addEventListener('click', (e) => {
      e.preventDefault()

      const target = e.target
      const operation = target.textContent

      const isInputSelected = inputs.find(input => input.checked)
      if (isInputSelected) {
        if (target.textContent === 'save') {
          target.textContent = 'edit'
        } else if (target.textContent === 'edit') {
          target.textContent = 'save'
        }
      }

      inputs.map((input, idx) => {
        
        if (input.checked) {

          const highlight_id = input.parentNode.id
          const comment = comments.find(comment => {
            if (comment.content.selection_text !== undefined) {
              if (comment.content.selection_text.id === highlight_id) {
                return comment
              }
            } else {
              if (comment.content.block_id === highlight_id) {
                return comment
              }
            }
          })

          const comment_input_text = input.parentNode.querySelector('.comment-label-wrapper > input')

          if (operation === 'save') {

            // save input text
            comment.content.text = comment_input_text.value
            comment_store.save(comment)

            comment_input_text.setAttribute('readonly', 'readonly')
            // comment_input_text.style.width = `${comment_input_text.value.length +1}ch`
            comment_input_text.style.borderColor = 'transparent'

            document.querySelector('body').focus()

          } else if (operation === 'edit') {

            moveCaretToEnd(comment_input_text)
            
            comment_input_text.removeAttribute('readonly')
            comment_input_text.style.width = 'auto'
            comment_input_text.style.borderColor = 'black'

          }

        }

      })

    })
 
    // -- remove selected comments
    remove_btn.classList.remove('hidden')

    remove_btn.addEventListener('click', (e) => {
      e.preventDefault()

      inputs.map(input => {
        if (input.checked) {
          const highlight_id = input.parentNode.id
          comment_store.remove(highlight_id)
          removeCommentDOM(highlight_id)
        }
      })

      commentReviewList(article_slug)
      updateCommentCounter('decrease', comments.length)
    })

  } else {
    publish_btn.classList.add('hidden')
    edit_btn.classList.add('hidden')
    remove_btn.classList.add('hidden')
  }
  
}

function make_comment_el(comment, idx, article_slug) {
  const comment_store = new LocalStore(`comment-${article_slug}`)

  // -- wrapper node
  const wrapper = document.createElement('div')
  wrapper.classList.add('comment-list-wrapper')
  wrapper.setAttribute('id', comment.id)

  // -- input checkbox
  const comment_input = document.createElement('input')
  comment_input.setAttribute('type', 'checkbox')
  comment_input.setAttribute('id', `comment_data[]`)
  comment_input.setAttribute('name', `comment_data[]`)
  comment_input.classList.add('comment-list-input')

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

  // -- add link to text-selection span
  const highlight_id = comment.id

  const target = document.querySelector(`[data-highlight-id="${highlight_id}"]`)
  if (target !== null) {
    target.setAttribute('id', highlight_id) 
  }

  const show_text_selection = document.createElement('a')
  show_text_selection.setAttribute('href', `#${highlight_id}`)
  show_text_selection.innerHTML = 'Show'
  show_text_selection.classList.add('comment-list-show-highlight')

  show_text_selection.addEventListener('click', (e) => {
    // set border around text-selection for a
    // specific amount of time, then reset the style

    target.style.border = '1px solid blue'

    setTimeout(() => {
      target.style.border = ''
    }, '3000')
  })

  // -- append above nodes to label wrapper node
  const comment_label_wrapper = document.createElement('div')
  comment_label_wrapper.classList.add('comment-label-wrapper')
  comment_label_wrapper.append(comment_input_text)

  // -- append wrapper nodes to label node
  comment_label.append(comment_label_wrapper)
  comment_label_wrapper.append(show_text_selection)

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

module.exports = { commentReviewList,
                   setUsername,
                   updateCommentCounter }
