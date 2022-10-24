import {
  $,
  $$,
  SelectionObserver,
  vue,
} from '../utils/index.js'

import Rows from './article/Rows.js'

const article_element    = $( 'main' )
const comment_forms      = $$( '.comment_form' )
const selection_observer = new SelectionObserver( article_element )

for ( const comment_form of comment_forms ) {
  comment_form.onsubmit = post_comment
}



console.log( data )

const { createApp, ref } = vue

const app = createApp({
  components: {
    Rows,
  },
  setup() {

    const message = ref('Hello World!')


    function notify( msg = message ) {
      console.log( msg )
      alert( msg )
    }


    return {
      data,
      message,
      notify
    }

  }
}).mount( article_element )











function post_comment( e ) {
  e.preventDefault()
  console.log( e )

  const form         = e.target
  const api_user     = form.getAttribute( 'data-auth-user' )
  const api_pass     = form.getAttribute( 'data-auth-pass' )
  const block_id     = form.getAttribute( 'data-block-id' )
  const article_slug = form.getAttribute( 'data-article-slug' )
  const author       = Array.from( form.children ).find( c => c.name == 'author' ).value
  const text         = Array.from( form.children ).find( c => c.name == 'body' ).value
  const selection    = selection_observer.selection

  console.log( selection )


  const auth = btoa([ api_user, api_pass ].join(':'))

  console.log( api_user, api_pass, auth )

  const url = 'comments'

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
      selection_type: '',
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

  fetch(`/api/pages/${ url }/children`, {
    method: "POST",
    headers: {
      Authorization: `Basic ${ auth }`
    },
    body: JSON.stringify( body )
  })
  .then(response => response.json())
  .then(response => {
    const page = response.data;
    console.log('kirby-api page =>', page)
  })
  .catch(error => {
    console.log('err =>', err)
  })
  return false
}
