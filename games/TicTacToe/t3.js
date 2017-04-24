var user = "X";
var opponent = "O";
var gameOver = false;
function getBoardArray() {
    var board = [];
    var i = 0;
    var data = $.makeArray($(".t3board td").map(
        function () {
            return $(this).text();
        })
    );
    for (i=0; i<9; i=i) {
        board.push(data.slice(i, i+=3));
    }
    return board;
}

function resetBoard() {
	user = "X";
	opponent = "O";
    gameOver = false;
	$("#EngineFirst").show();
	$(".t3board td").text("");
}

function checkForWinner() {
    $.ajax({
        type:"POST",
        data: JSON.stringify({
            jsonrpc:'2.0',
            method:'getWinner',
            params:{boardState: getBoardArray()},
            id:"web-win-check"
        }),
        success: function (responseString) {
            var response = JSON.parse(responseString);
            console.log(response);
            var message = '';
            switch (response.result) {
            	case null: //Game Not over
            		return true;
            	case false: //Draw Game
            		message = "The game was a Draw.";
            		break;
            	case user:  //User Wins
            		message = "You Win! Congratulations!";
            		break;
            	case opponent: //Opponent Wins
            		message = "You Lost, better luck next time!";
            		break;
            }
            gameOver = true;
            if(confirm(message + " Play Again?")) {
            	resetBoard();
            }
        }
    })
}

function makeMove() {
    var engine = $("#opponent").val();
    $.ajax({
        type:"POST",
        data: JSON.stringify({
            jsonrpc:'2.0',
            method:'makeMove',
            params: {
                boardState: getBoardArray(),
                playerUnit: opponent,
                "opponent": engine
            },
            id: "web-makeMove"
        }),
        success: function (responseString) {
            var response = JSON.parse(responseString);
            // console.log(response);
            var board = $(".t3board")[0];
            if(response.result) {
	            var cell = board.rows[response.result[1]].cells[response.result[0]]; // This is a DOM "TD" element
				$(cell).text(response.result[2]);
			}
        	checkForWinner()
        }
    })
}

$(document).ready(function(){
    $(".t3board").on("click", "td", function() {
        var col = $(this).index();
        var $tr = $(this).closest("tr");
        var row = $tr.index();
        // console.log(row, col);
		$("#EngineFirst").hide();
        if($(this).text() != "" || gameOver) {
        	return false;
        }
        $(this).text(user);
        checkForWinner();
        makeMove();
    });
    $("#EngineFirst").click(function() {
        user = "O";
        opponent = "X";
        $(this).hide();
        makeMove();
        return false;
    })
    $("#Reset").click(function() {
        resetBoard();
        return false;
    })
})
