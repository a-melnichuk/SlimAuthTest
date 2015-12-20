<form action="#" method="post" id="new-movie">
    <h2>New movie</h2>
    <label for="title">Title</label><br>
    <input pattern=".{1,}"  required type="text" name="title" id="title"><br>

    <label for="year">Year</label><br><!-- TIL: First movie was made in 1896 -->
    <input type="number" min="1896" max="<?= date('Y') ?>" name="year" id="year"><br>

    <label for="format">Format</label><br>
    <select name="format" id="format">
        <option value = "VHS">VHS</option>
        <option value = "DVD">DVD</option>
        <option value = "Blu-Ray">Blu-Ray</option>
    </select>
    <div id="actors_container">
        <p class = "actor_segment">
            <label for="actor_name" class ="actor_name_label">Actor Name</label><br>
            <input pattern=".{1,}"  required type="text" name="actor_name[]" class ="actor_name_input" ><br>
            <label for="actor_surname" class ="actor_surname_label">Actor Surname</label><br>
            <input pattern=".{1,}"  required type="text" name="actor_surname[]" class ="actor_surname_input"><br>
        </p>
    </div>
    <a href="#" id="add_actor">Add new actor (+)</a><br>
    <input type="submit" value="Submit" name="submit">
</form>
<script type="text/javascript">
document.addEventListener("DOMContentLoaded", function(e) { 
    document.getElementById("add_actor").addEventListener("click", function(e)
    {     
        e.preventDefault();
        
        var container = document.getElementById("actors_container");
        var actor_segment = cloneElement("actor_segment");
        
        actor_segment.childNodes[4].value = '';
        actor_segment.childNodes[10].value = '';
        
        var remove_link = document.createElement('a');
        remove_link.href = '#';
        remove_link.innerHTML = 'Remove actor (-)';
        
        actor_segment.appendChild(remove_link);
        addElement(container,actor_segment);
        
        remove_link.addEventListener("click", function(e){
            e.preventDefault();
            var parent = remove_link.parentElement;
            container.removeChild(parent);
        });
        
    });
       
    function addElement(container,added_element)
    {
        container.appendChild(added_element);
    }
    function cloneElement(added_element_class)
    {
       return document.getElementsByClassName(added_element_class)[0].cloneNode(true);
    }
    
});


    
    
    
    
</script>

