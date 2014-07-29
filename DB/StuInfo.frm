TYPE=VIEW
query=select `bluetooth`.`T_StuCourse`.`CourseClassNo` AS `CourseClassNo`,`bluetooth`.`T_StuInfo`.`StuName` AS `StuName`,`bluetooth`.`T_StuInfo`.`Grade` AS `Grade`,`bluetooth`.`T_StuInfo`.`DeoName` AS `DeoName`,`bluetooth`.`T_StuInfo`.`Major` AS `Major`,`bluetooth`.`T_StuInfo`.`BigClassName` AS `BigClassName`,`bluetooth`.`T_StuInfo`.`SmallClassName` AS `SmallClassName`,`bluetooth`.`T_StuInfo`.`BtMac` AS `BtMac`,`bluetooth`.`T_StuInfo`.`BtName` AS `BtName`,`bluetooth`.`T_StuInfo`.`StuNo` AS `StuNo` from ((`bluetooth`.`T_ID_list` join `bluetooth`.`T_StuCourse` on((`bluetooth`.`T_ID_list`.`CourseClassNo` = `bluetooth`.`T_StuCourse`.`CourseClassNo`))) join `bluetooth`.`T_StuInfo` on((`bluetooth`.`T_StuCourse`.`StuNo` = `bluetooth`.`T_StuInfo`.`StuNo`)))
md5=463a6e995cbcad892c2e5290ad00ae63
updatable=1
algorithm=0
definer_user=robin
definer_host=%
suid=1
with_check_option=0
timestamp=2012-11-13 13:11:29
create-version=1
source=SELECT\nT_StuCourse.CourseClassNo AS CourseClassNo,\nT_StuInfo.StuName AS StuName,\nT_StuInfo.Grade AS Grade,\nT_StuInfo.DeoName AS DeoName,\nT_StuInfo.Major AS Major,\nT_StuInfo.BigClassName AS BigClassName,\nT_StuInfo.SmallClassName AS SmallClassName,\nT_StuInfo.BtMac AS BtMac,\nT_StuInfo.BtName AS BtName,\nT_StuInfo.StuNo AS StuNo\nfrom ((`T_ID_list` join `T_StuCourse` on((`T_ID_list`.`CourseClassNo` = `T_StuCourse`.`CourseClassNo`))) join `T_StuInfo` on((`T_StuCourse`.`StuNo` = `T_StuInfo`.`StuNo`)))
client_cs_name=utf8
connection_cl_name=utf8_general_ci
view_body_utf8=select `bluetooth`.`T_StuCourse`.`CourseClassNo` AS `CourseClassNo`,`bluetooth`.`T_StuInfo`.`StuName` AS `StuName`,`bluetooth`.`T_StuInfo`.`Grade` AS `Grade`,`bluetooth`.`T_StuInfo`.`DeoName` AS `DeoName`,`bluetooth`.`T_StuInfo`.`Major` AS `Major`,`bluetooth`.`T_StuInfo`.`BigClassName` AS `BigClassName`,`bluetooth`.`T_StuInfo`.`SmallClassName` AS `SmallClassName`,`bluetooth`.`T_StuInfo`.`BtMac` AS `BtMac`,`bluetooth`.`T_StuInfo`.`BtName` AS `BtName`,`bluetooth`.`T_StuInfo`.`StuNo` AS `StuNo` from ((`bluetooth`.`T_ID_list` join `bluetooth`.`T_StuCourse` on((`bluetooth`.`T_ID_list`.`CourseClassNo` = `bluetooth`.`T_StuCourse`.`CourseClassNo`))) join `bluetooth`.`T_StuInfo` on((`bluetooth`.`T_StuCourse`.`StuNo` = `bluetooth`.`T_StuInfo`.`StuNo`)))
