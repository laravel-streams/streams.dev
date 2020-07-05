function appendListElement(list, headline) {
    const li = document.createElement("li");
    const a = document.createElement("a");

    a.appendChild(document.createTextNode(headline.innerHTML.trim()));
    a.href=`#${headline.innerHTML.trim()}`;
    li.appendChild(a);
    list.appendChild(li);

    const children = headline.querySelectorAll('h2','h3','h4','h5')

    console.log(children)
}

export function addAsideContent() {

    const content = document.querySelectorAll('.o-main h1');
    const aside = document.querySelector('.o-aside__nav');

    content.forEach(headline => {
        headline.id=headline.innerHTML.trim()
        appendListElement(aside, headline);
    });



}