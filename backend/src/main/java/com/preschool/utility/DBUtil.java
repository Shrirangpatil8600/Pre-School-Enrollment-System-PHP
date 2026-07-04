package com.preschool.utility;

import java.io.InputStream;
import java.sql.Connection;
import java.sql.DriverManager;
import java.util.Properties;

public class DBUtil {
    private static Connection conn = null;

    public static Connection getConnection() {
        try {
            if (conn == null || conn.isClosed()) {
                Properties props = new Properties();
                try (InputStream in = DBUtil.class.getClassLoader().getResourceAsStream("application.properties")) {
                    if (in == null) {
                        throw new RuntimeException("application.properties not found in classpath");
                    }
                    props.load(in);
                }
                Class.forName(props.getProperty("jdbc.driver"));
                conn = DriverManager.getConnection(
                    props.getProperty("jdbc.url"),
                    props.getProperty("jdbc.username"),
                    props.getProperty("jdbc.password")
                );
            }
        } catch (Exception e) {
            System.err.println("Database Connection Error: " + e.getMessage());
            e.printStackTrace();
        }
        return conn;
    }
}
