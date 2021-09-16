const courseList = document.querySelector("body > div.courselist")
const parser = new DOMParser()
courseList.addEventListener('click', e => {
  const selection = e.target.closest('.selection')
  const child = e.target
  const addBtn = child.closest('.add-btn')
  if (addBtn != null){
    let node = parser.parseFromString( 
    `
      <form class="selection" method="POST">
          <img class="insert-btn" src="./image/pencil.png" align="right">
          <img class="delete-btn" src="./image/bin.png" align="right">
          <input type="text" value="" name="name" placeholder="course-name">
          <input type="submit" class="insert-input" name="action" value="insert" style="display: none;" formaction="http://localhost:8080/course-action">
      </form>
    `, 'text/html').body.firstChild
    courseList.insertBefore(node, addBtn)
  }
  if (selection == null) return
  if (child.className == 'remove-btn'){
    const input = selection.querySelector('.remove-input')
    console.log(input)
    input.click()
  }
  else if (child.className == 'update-btn'){
    const input = selection.querySelector('.update-input')
    console.log(input)
    input.click()
  }
  else if (child.className == 'insert-btn'){
    const input = selection.querySelector('.insert-input')
    console.log(input)
    input.click()
  }
  else if (child.className == 'delete-btn'){
    let selection = e.target.closest('.selection')
    selection.remove()
  }
  else if (selection.className == 'selection'){
    const name = selection.querySelector('h2')
    if (name != null){
      const id = selection.getAttribute('id')
      window.location.href = `http://localhost:8080?lessonID=${id}&lessonName=${name.textContent}`
    }
  }
})

