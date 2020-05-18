WITH board_pages AS (
    SELECT
        round(avg(cnt), 2) as avg
    FROM (
         SELECT count(*) as cnt,
                parent_url
         FROM bitcointalk_board_pages
         GROUP BY parent_url
         ORDER BY cnt DESC
    ) as counts
), main_topics AS (
    SELECT
        round(avg(cnt), 2) as avg
    FROM (
         SELECT count(*) as cnt,
                parent_url
         FROM bitcointalk_main_topics
         GROUP BY parent_url
         ORDER BY cnt DESC
    ) as counts
), topics_pages AS (
    SELECT
        round(avg(cnt), 2) as avg
    FROM (
         SELECT count(*) as cnt,
                parent_url
         FROM bitcointalk_topic_pages
         GROUP BY parent_url
         ORDER BY cnt DESC
    ) as counts
), actual_counts AS (
    SELECT
        (SELECT count(*) FROM bitcointalk_board_pages) as act_bpages_cnt,
        (SELECT count(*) FROM bitcointalk_main_topics) as act_mtopics_cnt,
        (SELECT count(*) FROM bitcointalk_topic_pages) as act_tpages_cnt,
        (SELECT count(*) FROM bitcointalk_user_profiles) as act_uprofile_cnt
), user_profiles AS (
    SELECT ROUND(( 1.0 * act_uprofile_cnt / act_tpages_cnt), 2) as uprofile_avg
    FROM actual_counts
), avg_stats AS (
    SELECT
        count(*) as main_boards_cnt,
        (SELECT avg FROM board_pages) as bpages_avg,
        (SELECT avg FROM main_topics) as mtopics_avg,
        (SELECT avg FROM topics_pages) as tpages_avg
    FROM bitcointalk_main_boards
), predict_counts AS (
    SELECT
        main_boards_cnt,
        round(main_boards_cnt * bpages_avg, 2) as pred_bpages_cnt,
        round(main_boards_cnt * bpages_avg * mtopics_avg, 2) as pred_mtopics_cnt,
        round(main_boards_cnt * bpages_avg * mtopics_avg * tpages_avg, 2) as pred_tpages_cnt,
        round(main_boards_cnt * bpages_avg * mtopics_avg * tpages_avg * uprofile_avg, 2) as pred_uprofile_cnt
    FROM avg_stats, user_profiles
), pred_time AS (
    SELECT
        ROUND((main_boards_cnt + pred_bpages_cnt + pred_mtopics_cnt + 2 * pred_tpages_cnt + pred_uprofile_cnt) / (3600 * 24), 2) as pred_total_days
    FROM predict_counts
)
SELECT
       pred_total_days,
       main_boards_cnt,
       pred_bpages_cnt,
       pred_mtopics_cnt,
       pred_tpages_cnt,
       pred_uprofile_cnt,
       round(act_bpages_cnt / pred_bpages_cnt, 2) * 100 as bpages_perc,
       round(act_mtopics_cnt / pred_mtopics_cnt, 2) * 100 as mtopics_perc,
       round(act_tpages_cnt / pred_tpages_cnt, 2) * 100 as tpages_perc,
       round(act_uprofile_cnt / pred_uprofile_cnt, 2) * 100 as uprofile_perc
FROM predict_counts, actual_counts, pred_time

