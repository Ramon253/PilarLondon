export default function flashMessage(element){
    setTimeout(()=>{
        element.style.display = 'hidden'
    }, 5000)
}
