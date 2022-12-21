const LocalStore = require('./local.store')
const store = new LocalStore()

window.commenting_state = {
  commenting : false,
  comments   : {},
  author     : undefined,
}

// called on comment form "submit" action
function respond_comment( e ) {
  e.preventDefault()

  // enable commenting state if editing the form didnt do so
  if ( !window.commenting_state.commenting ) {
    enable_commenting_state()
  }

  // get form from event and make comment from it
  let form = e.target
  const comment = make_comment( form, store )
  const comment_el = make_comment_el( comment )

  // append new comment to comment thread
  // before <form> (blue circle)
  const thread = form.parentNode
  thread.insertBefore(comment_el, form)


  // if the form came from an "edit comment" action, we need
  // to delete it. We know this if it's got a commment id
  const comment_id = form.getAttribute( 'data-comment-id' )
  if ( comment_id ) {
    form.parentElement.removeChild( form )
    const comment_el = document.getElementById( comment_id )
    form = get_nearest_form( comment_el )
  }

  reset_and_focus( form, comment )

}





// Commenting is a state of mind

function enable_commenting_state() {
  // enable commenting in global state
  window.commenting_state.commenting = true
  // add commenting class to body
  document.body.classList.add( 'commenting' )
  // prevent accidental refershes
  window.onbeforeunload = e => { e.preventDefault() }
  // update editor toolbar to appear and be usable
  enable_editor_toolbar()
}

function disable_commenting_state() {
  // disable it in our global state
  window.commenting_state.commenting = false
  // remvoe commenting class from body
  document.body.classList.remove( 'commenting' )
  // remove the window reload blocker
  window.onbeforeunload = null
  // disable editor toolbar
  disable_editor_toolbar()
  // get all edit and delete buttons and remove them
  freeze_my_comment_els()
  // update count in UI
  update_own_comment_count()
}



function edit_comment( slug ) {
  const comment = get_comment( slug )
  const { user, text } = comment.content
  const comment_el = document.getElementById( slug )
  const template_form = get_nearest_form( comment_el )
  const form = make_comment_form_el( template_form )
  form.setAttribute( 'data-comment-id', slug )
  form.querySelector( 'input[name="author"]' ).value = user
  form.querySelector( 'input[name="text"]' ).value = text
  comment_el.replaceWith( form )
  form.firstElementChild.focus()
}

function get_comment_count() {
  return Object.values( window.commenting_state.comments ).length
}

function get_comment( slug ) {
  return window.commenting_state.comments[slug]
}

function delete_comment( slug ) {
  delete window.commenting_state.comments[slug]
  delete_comment_el( slug )
  update_own_comment_count()
}

function make_comment( form, store ) {
  const chilren           = Array.from( form.children )
  const csrf              = form.getAttribute( 'data-csrf' )
  const article_slug      = form.getAttribute( 'data-article-slug' )
  const block_id          = form.getAttribute( 'data-block-id' )
  const selection_type    = form.getAttribute( 'data-block-selection-type' )
  const selection_text_id = form.getAttribute( 'data-block-selection-text-id' )
  const selection_text    = store.getByID(selection_text_id)
  const author            = chilren.find( c => c.name == 'author' ).value
  const text              = chilren.find( c => c.name == 'text' ).value
  const ts                = new Date().toISOString()
  const comment_id        = form.getAttribute( 'data-comment-id' ) || `comment-${ ts }`
  const comment           = {
    slug: comment_id,
    title: '',
    template: 'comment',
    csrf: csrf,
    content: {
      user: author,
      timestamp: ts,
      article_slug: article_slug,
      block_id: block_id,
      text: text,
      selection_type: selection_type,
      selection_text: selection_text,
      // selection_coords: selection_coords
    }
  }
  window.commenting_state.comments[comment.slug] = comment
  update_own_comment_count()
  return comment
}






// JUST API STUFF 💅

async function post_comments( e ) {
  e.preventDefault()
  for ( const comment of Object.values( window.commenting_state.comments ) ) {
    await post_comment( comment )
  }
  window.commenting_state.comments = {}
  disable_commenting_state()
}

function post_comment( comment ) {
  const csrf = comment.csrf
  const article_slug = comment.content.article_slug
  const url  = `/api/pages/articles+${ article_slug }+comments`
  return fetch( `${ url }/children` , {
    method: "POST",
    headers: { "X-CSRF": csrf },
    body: JSON.stringify( comment )
  })
  .then(response => response.json())
  .then(response => {
    // set comment to be published
    return fetch( `${ url }+${ response.data.slug }/status`, {
      method: "PATCH",
      headers: { "X-CSRF": csrf },
      body: JSON.stringify( { status: 'listed' } )
    })
    .then(response => response.json())
    .then( response => response )
    .catch( error => console.error( error ) )
  })
  .then(response => response)
  .catch(error => console.error(error) )
}





// MANIPULATING DOM

function update_own_comment_count() {
  const count = get_comment_count()
  const own_comment_count_els = document.querySelectorAll('.own_comment_count')
  Array.from( own_comment_count_els ).map( el => {
    el.innerHTML = count
  })
  if ( count ) {
    enable_editor_toolbar_post_button()
  } else {
    disable_editor_toolbar_post_button()
  }
}

function get_nearest_form( comment_el ) {
  return comment_el.parentElement.querySelector('.comment_form')
}

function reset_and_focus( form, comment ) {
  // reset form bring
  form.reset()
  // set name of author in UI
  set_author_name( comment.content.user )
  // focus to input to continue commenting
  form.firstElementChild.focus()
}

function set_author_name( author ) {
  // set it in the global state variable
  window.commenting_state.author = author
  Object.values( window.commenting_state.comments ).map( comment => {
    comment.content.user = author
  })
  // set it for all the forms
  const comment_forms = document.querySelectorAll( '.comment_form' )
  for ( const comment_form of comment_forms ) {
    const author_input = comment_form.querySelector( 'input[name="author"]' )
    author_input.value = author
    author_input.setAttribute('tabindex', '-1')
  }
  // set it for all of your own comment elements
  const comment_els = document.querySelectorAll( 'aside article.mine' )
  for ( const comment_el of comment_els ) {
    const author_el = comment_el.querySelector( '.author' )
    author_el.innerHTML = author
  }
}

function enable_editor_toolbar() {
  const editor_toolbar = document.getElementById( 'editor_toolbar' )
  editor_toolbar.onsubmit = post_comments
}

function disable_editor_toolbar() {
  const editor_toolbar = document.getElementById( 'editor_toolbar' )
  editor_toolbar.onsubmit = null
}

function enable_editor_toolbar_post_button() {
  const post_button = document.getElementById( 'editor_toolbar' ).lastElementChild
  post_button.removeAttribute( 'disabled' )
}

function disable_editor_toolbar_post_button() {
  const post_button = document.getElementById( 'editor_toolbar' ).lastElementChild
  post_button.setAttribute( 'disabled', true )
}

function freeze_my_comment_els() {
  const my_comment_els = document.querySelectorAll( 'article.mine' )
  for ( const comment_el of my_comment_els ) {
    comment_el.classList.remove( 'mine' )
    comment_el.removeChild( comment_el.firstElementChild )
  }
}

function delete_comment_el( slug ) {
  const comment_el = document.getElementById( slug )
  comment_el.parentElement.removeChild( comment_el )
}







// MAKING HTML ELEMENTS

function make_comment_thread_el( form, position ) {
  const thread = document.createElement( 'section' )
  thread.classList.add( 'thread' )
  thread.classList.add( 'new' )
  thread.appendChild( form )
  thread.style.setProperty( '--top', position.top + 'px' )
  thread.style.setProperty( '--left', position.left + 'px' )
  return thread
}

function make_comment_form_el( template_form, source_id, block_id ) {
  const form = template_form.cloneNode( true )
  form.onsubmit = respond_comment
  form.setAttribute('data-block-selection-type', 'text')
  if ( source_id ) {
    form.setAttribute('data-block-selection-text-id', source_id )
  }
  if ( block_id ) {
    form.setAttribute('data-block-id', block_id )
  }
  return form
}

function make_comment_el( data ) {

  // -- header
  const header = document.createElement( 'section' )
  header.classList.add( 'header' )
  const delete_button = document.createElement( 'input' )
  delete_button.setAttribute( 'type', 'button' )
  delete_button.setAttribute( 'value', 'delete comment' )
  delete_button.setAttribute( 'name', 'delete_comment' )
  delete_button.onclick = e => delete_comment( data.slug )
  header.appendChild( delete_button )
  const edit_button = document.createElement( 'input' )
  edit_button.setAttribute( 'type', 'button' )
  edit_button.setAttribute( 'value', 'edit comment' )
  edit_button.setAttribute( 'name', 'edit_comment' )
  edit_button.onclick = e => edit_comment( data.slug )
  header.appendChild( edit_button )

  // -- section
  const text_comment = document.createElement('section')
  const section_text = data.content.text
  text_comment.append(section_text)

  // -- footer
  const footer = document.createElement('footer')
  footer.classList.add( 'small_italic' )
  const date = document.createElement('p')
  const timestamp = document.createElement('time')
  // TODO set correct datetime format for timestamp `yyyy-mm-dd hh:mm:ss`
  timestamp.setAttribute('datetime', data.content.timestamp)
  date.append(timestamp)
  date.innerHTML = `On ${data.content.timestamp}`

  const user = document.createElement('p')
  user.innerHTML = `by <span class="author">${data.content.user}</span>`

  footer.append(date)
  footer.append(user)

  // -- append everything to <article>
  const article = document.createElement('article')
  article.id = data.slug
  article.classList.add( 'mine' )
  article.setAttribute('tabindex', '0')

  article.append(header)
  article.append(text_comment)
  article.append(footer)

  return article

}

module.exports = {
  enable_commenting_state,
  respond_comment,
  post_comment,
  make_comment_el,
  make_comment_thread_el,
  make_comment_form_el,

}
