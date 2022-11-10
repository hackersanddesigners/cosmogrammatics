import {
  $,
  $$,
  SelectionObserver
} from '../utils/index.js'

const article_element    = $( 'main' )
const comment_forms      = $$( '.comment_form' )
const selection_observer = new SelectionObserver( article_element )

for ( const comment_form of comment_forms ) {
  comment_form.onsubmit = respond_to_comment
}

function respond_to_comment( e ) {
  e.preventDefault()
  const form    = e.target
  const chilren = Array.from( form.children )
  post_comment({
    article_slug   : form.getAttribute( 'data-article-slug' ),
    block_id       : form.getAttribute( 'data-block-id' ),
    csrf           : form.getAttribute( 'data-csrf' ),
    author         : chilren.find( c => c.name == 'author' ).value,
    text           : chilren.find( c => c.name == 'body' ).value,
    selection_type : 'text',
  })
  .then( response => {
    form.reset()
  })
}

function post_comment( comment ) {

  const {
    article_slug,
    block_id,
    csrf,
    author,
    text,
    selection_type
  } = comment

  const ts   = new Date().toISOString().split('.')[0]+"Z"
  const url  = `/api/pages/articles+${ article_slug }+comments`
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

  return fetch( `${ url }/children` , {
    method: "POST",
    headers: { "X-CSRF": csrf },
    body: JSON.stringify( body )
  })
  .then(response => response.json())
  .then(response => {
    fetch( `${ url }+${ response.data.slug }/status`, {
      method: "PATCH",
      headers: { "X-CSRF": csrf },
      body: JSON.stringify( { status: 'listed' } )
    })
    .then(response => response.json())
    .then( response => response )
    .catch( error => console.error( error ) )
  })
  .then(response => response )
  .catch(error => console.error(error) )


}
