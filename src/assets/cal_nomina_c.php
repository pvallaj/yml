CREATE DEFINER=`yamellwf`@`localhost` PROCEDURE `calcula_nomina_empl`(IN `p_id_nom` INT, IN `p_id_empl` INT, IN `p_sueldo` FLOAT, IN `p_complemento` FLOAT, IN `p_ap_tedd` BOOLEAN, IN `p_apv` BOOLEAN )
BEGIN
  DECLARE v_sueldo FLOAT default 0;
  DECLARE v_complemento FLOAT default 0;
  DECLARE v_comisones, v_compl_edd, v_sueldo_edd FLOAT default 0;
  DECLARE v_pv_sueldo, v_pv_compl FLOAT default 0;
  DECLARE fi, ff DATE;
  start transaction;
  delete from nomina_detalle where id_nomina=p_id_nom and no_empl=p_id_empl;

  IF p_ap_tedd=true THEN
    /*set v_sueldo_edd=p_sueldo/7;*/
    set v_sueldo=p_sueldo+v_sueldo_edd;
    insert into nomina_detalle (id_nomina, no_empl, no_regla, valor)
    values(p_id_nom, p_id_empl, 5, v_sueldo_edd);

    set v_compl_edd=p_complemento/7;
    set v_complemento=p_complemento+v_compl_edd;
    insert into nomina_detalle (id_nomina, no_empl, no_regla, valor)
    values(p_id_nom, p_id_empl, 4, v_compl_edd);
  ELSE
    set v_sueldo=p_sueldo;
    set v_complemento=p_complemento;
  END IF;

  insert into nomina_detalle (id_nomina, no_empl, no_regla, valor)
  values(p_id_nom, p_id_empl, 3, v_sueldo);

  insert into nomina_detalle (id_nomina, no_empl, no_regla, valor)
  values(p_id_nom, p_id_empl, 2, v_complemento);

  IF p_apv=true THEN
    v_pv_sueldo=v_sueldo*0.25;
    v_pv_compl=v_complemento*0.25;

    insert into nomina_detalle (id_nomina, no_empl, no_regla, valor)
    values(p_id_nom, p_id_empl, 7, v_pv_sueldo);

    insert into nomina_detalle (id_nomina, no_empl, no_regla, valor)
    values(p_id_nom, p_id_empl, 8, v_pv_compl);
  END IF;

  select inicio, termino into fi, ff
  from nominas 
  where id=p_id_nom;

  select sum(comisiones)
  into v_comisones
  from comisiones_diarias 
  where 
    fecha>=fi 
    and fecha<=ff
    and id=p_id_empl;

  insert into nomina_detalle (id_nomina, no_empl, no_regla, valor)
  values(p_id_nom, p_id_empl, 1, v_comisones);

  insert into nomina_detalle (id_nomina, no_empl, no_regla, valor)
  values(p_id_nom, p_id_empl, 6, v_comisones+v_complemento+v_pv_compl);

  insert into nomina_detalle (id_nomina, no_empl, no_regla, valor)
  values(p_id_nom, p_id_empl, 100, v_comisones+v_complemento+v_sueldo+v_pv_compl+v_pv_sueldo);

  commit;
END