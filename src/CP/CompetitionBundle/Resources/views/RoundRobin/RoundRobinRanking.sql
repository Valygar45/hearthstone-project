with results as
( select select()
, g.team1
, g.score1
, g.team2
, g.score2
from game g
union all
select mr.group1
, mr.away
, mr.away_goals
, mr.home
, mr.home_goals
from match_results mr
)
, team_results as
( select group1
, home team
, count(*) over (partition by group1, home) games_played
, sum( case
when home_goals > away_goals then 3
when away_goals > home_goals then 0
else 1 end
) over (partition by group1, home) points
, sum( home_goals) over (partition by group1, home) goals_scored
, sum( away_goals) over (partition by group1, home) goals_conceded
from results
)
select distinct
tr.group1
, t.country
, games_played
, points
, goals_scored-goals_conceded goal_difference
, goals_scored
from team_results tr
join
teams t
on (tr.team = t.seq_in_group
and
tr.group1 = t.group1
)
order
by tr.group1
, points desc
, goal_difference desc
, goals_scored desc




SELECT ID, COUNT(*) MatchPlayed,
SUM( CASE WHEN MatchResult = 'Won' THEN 3 WHEN MatchResult = 'Tied' THEN 1 ELSE 0 END ) as Points,
SUM( CASE WHEN MatchResult = 'Won' THEN 1 ELSE 0 END ) MatchWon,
SUM( CASE WHEN MatchResult = 'Tied' THEN 1 ELSE 0 END ) MatchTied,
SUM( CASE WHEN MatchResult = 'Lost' THEN 0 ELSE 0 END ) MatchLost,
SUM(goals_scored-goals_conceded) AS diff
FROM
(
	SELECT team1 AS ID,
	CASE WHEN game.score>game.score2 THEN 'Won'
	WHEN game.score<game.score2 THEN 'Lost'
	WHEN game.score=game.score2 THEN 'Tied'
	END AS MatchResult,game.score AS goals_scored, game.score2 as goals_conceded
	FROM game, round, round_robin, round_robin_round
	WHERE game.id=round.game_id and round_robin.id = round_robin_round.round_robin_id and round.id = round_robin_round.round_id and round_robin.id = 1

	UNION ALL
	SELECT team2 AS ID,
	CASE WHEN game2.score<game2.score2 THEN 'Won'
	WHEN game2.score>game2.score2 THEN 'Lost'
	WHEN game2.score=game2.score2 THEN 'Tied'
	END AS MatchResult, game2.score2 AS goals_scored, game2.score as goals_conceded
	FROM game as game2, round as round2, round_robin as round_robin2, round_robin_round as round_robin_round2
	WHERE game2.id=round2.game_id and round_robin2.id = round_robin_round2.round_robin_id and round2.id = round_robin_round2.round_id and round_robin2.id = 1

) A
GROUP BY IDa
ORDER BY Points desc, diff desc