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
  console.log( e )

  const auth = btoa([
    "<?= $kirby->option('env')['api_user'] ?>",
    "<?= $kirby->option('env')['api_pass'] ?>"
  ].join(':'))

  console.log( auth )

  const url = 'comments'

  // convert this to local timezone? or at least in the kirby panel
  const ts = new Date().toISOString().split('.')[0]+"Z"

  const body = {
    slug: `test-${ ts }`,
    title: '',
    template: 'comment',
    content: {
      user: 'sh',
      timestamp: ts,
      article_slug: '',
      block_id: '',
      text: '',
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
