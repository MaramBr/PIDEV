$("#new_edit_utilisateur").on('submit', function(){
    if($("#verifpass2").val() != $("#verifpass").val()) {
        //implémntez votre code
        alert($("#verifpass2").val());
        alert($("#verifpass").val());
        return false;
    }
})