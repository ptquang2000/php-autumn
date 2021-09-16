const content = document.querySelector(".content")
const parser = new DOMParser()
const child_content = content.children[0]
if (child_content != null && child_content.nodeName == 'FORM'){
  content.addEventListener('click', e => {
    const answer = e.target
    const addBtn = answer.closest('.add-wrapper')
    if (addBtn != null){
      const formaction = "http://localhost:8080/quiz-action"
      const num = addBtn.closest('.content').childElementCount
      const urlSearchParams = new URLSearchParams(window.location.search)
      const params = Object.fromEntries(urlSearchParams.entries())
      let node = parser.parseFromString( 
      `
          <form class="question" method="POST">
              <div class="q-head">
                  <p class="num">
                      Question${num}
                      <img class="insert btn" src="./image/pencil.png" align="right">
                      <input type="submit"  class="insert-input" value="insert" name="action" style="display: none;" formaction="${formaction}">
                      <img class="delete btn" src="./image/bin.png" align="right">
                  </p>
                  <p class="q-test" contenteditable="true">question</p>
                  <input style="display: none;" class="q-question" type="text" name="question" formaction="${formaction}">
                  <input style="display: none;" type="text" name="idCourse" value="${params.lessonID}" formaction="${formaction}">
              </div>
              <div class="q-ans">
                  <label class="ans">
                      <input type="radio" name="ans" value="1">
                      <span class="ans-span" contenteditable="true">answer1</span>
                      <input class="ans-input" type="text" name="ans1" value="" style="display: none;">
                      <span class="q-check"></span>
                  </label>
                  <label class="ans">
                      <input type="radio" name="ans" value="2">
                      <span class="ans-span" contenteditable="true">answer2</span>
                      <input class="ans-input" type="text" name="ans2" value=""  style="display: none;">
                      <span class="q-check"></span>
                  </label>
                  <label class="ans">
                      <input type="radio" name="ans" value="3">
                      <span class="ans-span" contenteditable="true">answer3</span>
                      <input class="ans-input" type="text" name="ans3" value=""  style="display: none;">
                      <span class="q-check"></span>
                  </label>
                  <label class="ans">
                      <input type="radio" name="ans" value="4">
                      <span class="ans-span" contenteditable="true">answer4</span>
                      <input class="ans-input" type="text" name="ans4" value="" style="display: none;">
                      <span class="q-check"></span>
                  </label>
              </div>
              <input type="text" name="course.id" value="${params.lessonID}" style="display: none;">
              <input type="text" name="course.name" value="${params.lessonName}" style="display: none;">
          </form>
      `, 'text/html').body.firstChild
      content.insertBefore(node, addBtn)
    }
    if (answer.className.toLowerCase() == 'delete btn'){
      let question = e.target.closest('.question')
      question.remove()
    }
    if (answer.className.toLowerCase() == 'remove btn'){
      const question = e.target.closest('.question')
      const input = question.querySelector('.remove-input')
      input.click()
    }
    if (answer.className.toLowerCase() == 'update btn'){
      const question = e.target.closest(".question")
      let head = question.querySelector('.q-head')
      head.querySelector(".q-question").setAttribute('value', head.querySelector('.q-test').textContent)

      let label = question.querySelector('.q-ans').querySelectorAll('label')
      for (let i = 0; i < label.length; i ++)
        label[i].querySelector('.ans-input').setAttribute('value',label[i].querySelector('.ans-span').textContent)
      const input = question.querySelector('.update-input')
      input.click()
    }
    if (answer.className.toLowerCase() == 'insert btn'){
      const question = e.target.closest(".question")
      let head = question.querySelector('.q-head')
      head.querySelector(".q-question").setAttribute('value', head.querySelector('.q-test').textContent)

      let label = question.querySelector('.q-ans').querySelectorAll('label')
      for (let i = 0; i < label.length; i ++)
        label[i].querySelector('.ans-input').setAttribute('value',label[i].querySelector('.ans-span').textContent)
      const input = question.querySelector('.insert-input')
      input.click()
    }
  })
};
  
if (child_content != null && child_content.nodeName == 'DIV')
  content.addEventListener('click', e => {
    const answer = e.target
    if (answer.tagName.toLowerCase() == 'input') {
      const question = e.target.closest('.question')
      const radios = question.querySelectorAll('input')
      const correctAnswer = question.getAttribute('answer')
      if (answer.value == correctAnswer){
        answer.closest('label').style.color = '#04FF57'
      }
      else{
        answer.closest('label').style.color = '#D6040D'
        question.querySelector(`div.q-ans > label:nth-child(${correctAnswer})`).style.color = '#04FF57'
      }
      for (let i =0; i < radios.length; i++)
        radios[i].disabled = true
    }
  })

const quizlet = document.querySelector("body > div.menu-bar > div.logo")
quizlet.addEventListener('click', e => {
  window.location.href = `http://localhost:8080/`
})