import {
  $,
  $$,
  SelectionObserver
} from '../utils/index.js'

const article_element    = $( 'main' )
const comment_forms      = $$( '.comment_form' )
const selection_observer = new SelectionObserver( article_element )

for ( const comment_form of comment_forms ) {
  comment_form.onsubmit = post_comment
}



function post_comment( e ) {
  e.preventDefault()

  const form           = e.target
  const form_chilren   = Array.from( form.children )
  const block_id       = form.getAttribute( 'data-block-id' )
  const article_slug   = form.getAttribute( 'data-article-slug' )
  const csrf           = form.getAttribute( 'data-csrf' )
  const author         = form_chilren.find( c => c.name == 'author' ).value
  const selection_type = 'text'
  const text           = form_chilren.find( c => c.name == 'body' ).value
  const selection      = selection_observer.selection
  const url            = `/api/pages/articles+${ article_slug }+comments/children`


  // console.log( selection )


  // convert this to local timezone? or at least in the kirby panel
  const ts = new Date().toISOString().split('.')[0]+"Z"

  const body = {
    slug: `test-${ ts }`,
    title: '',
    template: 'comment',
    content: {
      user: author,
      timestamp: ts,
      article_slug: article_slug,
      block_id: block_id,
      text: text,
      selection_type: selection_type,
      selection_text: {
        x: '',
        y: ''
      },
      selection_image: {
        x1: '',
        y1: '',
        x2: '',
        y2: ''
      },
      selection_audio: {
        t1: '',
        t2: ''
      },
      selection_video: {
        x1: '',
        y1: '',
        t1: '',
        x2: '',
        y2: '',
        t2: ''
      }
    }
  }

  fetch( url , {
    method: "POST",
    headers: { "X-CSRF": csrf },
    body: JSON.stringify( body )
  })
  .then(response => response.json())
  .then(response => {
    const page = response.data;
    console.log('kirby-api page =>', page)
  })
  .catch(error => {
    console.error(error)
  })
  // return false
}
