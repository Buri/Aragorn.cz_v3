function folder(user){
fail=0
user.trim
pf=parent.play.document.forms['chat']['test'].value
if (pf.length>0){

ch=pf.split(",")

for (i=0;i<ch.length;i++){
if (ch[i]==user){
fail=1;
}
}

if (fail!=1){
parent.play.document.forms['chat']['test'].value=parent.play.document.forms['chat']['test'].value+","+user
}

else{
tf=parent.play.document.forms['chat']['test'].value.split(",")
parent.play.document.forms['chat']['test'].value=""
fu=""
for(v=0;v<tf.length;v++){

if (tf[v]==user){

}else{
if (fu.length<1){
fu=fu.concat(tf[v])
}else{
fu=fu.concat(","+tf[v])
}
}

}
parent.play.document.forms['chat']['test'].value=fu
}

}
else{
parent.play.document.forms['chat']['test'].value=user;
}
}
