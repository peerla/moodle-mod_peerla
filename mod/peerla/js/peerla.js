$(function(){
    
    //info boxes
    $('.infoBox .infoMoreTextLink').click(function(){
        var moreContainer = $(this).siblings('.infoTextMore');
        
        if (moreContainer.length > 0){
            var toggleLink = $(this);
            var toggleText = $(this).attr('data-toggletext');
            var toggleLinkText = toggleLink.find('.toggleText');
            
            moreContainer.toggle();
            
            toggleLink.attr('data-toggletext',toggleLinkText.html());
            toggleLinkText.html(toggleText);
            toggleLink.find('.glyphicon').toggleClass('glyphicon-menu-down, glyphicon-menu-up');
        }
        
        return false;
    });
    
    //course goal form
    $('#addCourseGoal').click(function(){
            var goalCount = $('.goalContainer').size();
            var goalContainer = $('.goalContainer:first').clone();
            goalContainer.find('label').html('Ziel '+(goalCount+1));
            goalContainer.find('input.goalText').val('');
            goalContainer.find('input.goalText').attr('name','goals['+(goalCount+1)+'][text]');
            goalContainer.find('input.goalId').val('');
            goalContainer.find('input.goalId').attr('name','goals['+(goalCount+1)+'][goalId]');
            $('.goalContainer:last').after(goalContainer);
            return false;
    });
    
    //show subtopics for course topic
    $('.showSubTopics').click(function(){
        $(this).siblings('.courseTopicPrioList').show();
        $(this).hide();
        $(this).siblings('.hideSubTopics').show();
        $(this).siblings('.slider').slider( "option", "disabled", true );
        return false;
    });
    
    //show subtopics for course topic
    $('.hideSubTopics').click(function(){
        $(this).siblings('.courseTopicPrioList').hide();
        $(this).hide();
        $(this).siblings('.showSubTopics').show();
        $(this).siblings('.slider').slider( "option", "disabled", false );
        return false;
    });
    
    //sliders
    $('.slider').slider({
        min: 0,
        max: 100,
        step: 1,
        change: function(event, ui){
           var value = $(this).slider( "value" );
           $(this).siblings('.sliderValue').val(value);
           
           //only change this slider value, if the event was not manually generated
           //(prevents endless generation of new change events)
           if (!event.originalEvent){
               return;
           }
           
            //change subtopics
            $(this).siblings('.courseTopicPrioList').find('.slider')
                   .slider("option", "value", value);
           
           //change parent topics
           changeParentSliders($(this));
        },
        create: function(){
            var value = $(this).siblings('.sliderValue').val();
            $(this).slider("option", "value", value);
        }
    });
    
    //topic select slider values
    $('.courseTopicPrioList .slider');
    
    
    //interval goal form - add new goal
    $('.addIntervalGoal').click(addIntervalGoalClickHandler);
    
    //interval goal form - remove goal
    $('.removeIntervalGoal').click(removeIntervalGoalClickHandler);
    
    
});

//Set all sliders to the aggregated values of their children
function changeParentSliders(sliderElement){
    var childSliderContainer = sliderElement.parent('li').parent('ul.courseTopicPrioList');
    var parentSlider = childSliderContainer.siblings('.slider');
    
    //no parent slider? -> change nothing
    if (parentSlider.size() == 0){
        return;
    }
    
    var aggregatedValue = 0;
    //get the values of child sliders
    var children = childSliderContainer.find('> li >.slider');
    children.each(function(){
        aggregatedValue += $(this).slider("value");;
    });
    
    aggregatedValue = aggregatedValue / children.size();
    parentSlider.slider("option", "value", aggregatedValue);
    
    //change the parent of the next level
    changeParentSliders(parentSlider);
}

/*
 * 
 */
function addIntervalGoalClickHandler(){
    
    var goalCount = $('.intervalGoalContainer').size();
    var firstGoalContainer = $('.intervalGoalContainer:first');
    var newGoalContainer = firstGoalContainer.clone();
    
    var newIndex = goalCount+1;
    
    setIntervalGoalIndex(newGoalContainer, newIndex);
    
    newGoalContainer.find('.removeIntervalGoal').click(
            removeIntervalGoalClickHandler);

    $('.intervalGoalContainer:last').after(newGoalContainer);
    
    //unset data
    newGoalContainer.find('.topicSelect:eq(1)').prop('selected',true);
    newGoalContainer.find('.actionSelect:eq(1)').prop('selected',true);
    newGoalContainer.find('.planedTimeInvestmentInput').val('');
    newGoalContainer.find('.commentInput').val('');
    newGoalContainer.find('.intervalGoalIdInput').val('');
    newGoalContainer.find('.intervalStatus').val('');
    newGoalContainer.find('.dayCheckbox').prop('checked',false);
    newGoalContainer.find('.goalCreateTimeBox').html('');
    
    //display time avg
    newGoalContainer.find('.topicSelect:first').change(function(){displayTimeAvg(newGoalContainer);});
    newGoalContainer.find('.actionSelect:first').change(function(){displayTimeAvg(newGoalContainer);});
    newGoalContainer.find('.planedTimeInvestmentInput').change(function(){displayTimeAvg(newGoalContainer);});
    
    return false;
}

function setIntervalGoalIndex(containerElement, newIndex){
    
    //set goal number
    containerElement.find('.intervalGoalNumber').html(newIndex);
    //unset input element values
    containerElement.find('.topicSelect')
            .prop('name','goal['+newIndex+'][topicSelect]');
    containerElement.find('.actionSelect')
            .prop('name','goal['+newIndex+'][actionSelect]');
    containerElement.find('.planedTimeInvestmentHours')
            .prop('name','goal['+newIndex+'][planedTimeInvestmentHours]');
    containerElement.find('.planedTimeInvestmentMinutes')
            .prop('name','goal['+newIndex+'][planedTimeInvestmentMinutes]');
    containerElement.find('.commentInput')
            .prop('name','goal['+newIndex+'][comment]');
    containerElement.find('.intervalGoalIdInput')
            .prop('name','goal['+newIndex+'][intervalGoalId]');
    containerElement.find('.intervalStatusInput')
            .prop('name','goal['+newIndex+'][intervalStatus]');
    containerElement.find('.dayCheckbox')
            .prop('name','goal['+newIndex+'][weekday][]');
}

/*
 * Click-Handler function for interval goal remove links
 */
function removeIntervalGoalClickHandler(){
    var parentContainer = $(this).parents('.intervalGoalContainer');
    var goalCount = $('.intervalGoalContainer').size();
    var goalNumber = parseInt(parentContainer.find('.intervalGoalNumber').html());

    //don't remove the last goal container
    if (goalCount <= 1){
        return false;
    }

    parentContainer.nextAll('.intervalGoalContainer').each(function(index){
        setIntervalGoalIndex($(this), goalNumber+index);
    });

    parentContainer.remove();
    return false;
}


function displayTimeAvg(goalContainer){
    var topicId = goalContainer.find('.topicSelect:first').val();
    var actionId = goalContainer.find('.actionSelect:first').val();
    var courseId = $('#courseId').val();
    $.getJSON(
        '../ajax/getAvgTimeInvestment.php',
        {topicId: topicId, actionId: actionId, courseId: courseId},
        function(data){
                var text = 'Es liegen noch keine Daten zum Zeitinvestment vor.';
                if (data['investedAvg'] != null){
                        text = 'Durchschnittliches Zeitinvestment: '+data['investedAvg'];
                }

                goalContainer.find('.goalCreateTimeBox').html(text);
        }
    );
}