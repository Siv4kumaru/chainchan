from classes.super_models import chain_chan_models # Ensure this path is correct
from langchain_community.utilities import SQLDatabase

LLM_INSTANCE = None
DB_INSTANCE = None
ALL_TABLE_SCHEMAS_STRING = None # Will hold the schema for all tables
class chain_chan_database:
    def initialize_components():
        """
        Initializes the LLM, Database connection, and retrieves the database schema.
        This should be called once before using get_answer_from_database.
        Returns True on success, False on failure.
        """
        global LLM_INSTANCE, DB_INSTANCE, ALL_TABLE_SCHEMAS_STRING

        # 1. Initialize LLM
        try:
            LLM_INSTANCE = chain_chan_models(model="claude-3-haiku", temperature=0)
        except ImportError:
            print("ERROR: Could not import 'chain_chan_models' from 'classes.super_models'.")
            print("Please ensure 'classes/super_models.py' exists and is correctly set up.")
            return False
        except Exception as e:
            print(f"ERROR: Could not initialize the custom LLM: {e}")
            return False

        # 2. Initialize Database Connection
        db_folder = "db"
        db_file_name = "DataCoSupplyChainDataset.db"
        db_path = os.path.join(db_folder, db_file_name)
        db_uri = f"sqlite:///{db_path}"



        try:
            DB_INSTANCE = SQLDatabase.from_uri(db_uri, sample_rows_in_table_info=0)
            print(f"Database '{db_file_name}' connected successfully.")
        except Exception as e:
            print(f"ERROR: Could not connect to or initialize the database at '{db_uri}': {e}")
            return False

        # 3. Retrieve Schema for ALL tables
        try:
            all_table_names_in_db = DB_INSTANCE.get_usable_table_names()
            if not all_table_names_in_db:
                print("ERROR: No tables found in the database.")
                return False
            ALL_TABLE_SCHEMAS_STRING = DB_INSTANCE.get_table_info(all_table_names_in_db)
            print("Database schema retrieved successfully.")
        except Exception as e:
            print(f"ERROR: Could not retrieve table schemas from the database: {e}")
            return False
        
        return True


    def get_answer_from_database(user_question: str) -> str:
        """
        Takes a natural language question, queries the pre-configured database
        using the pre-initialized LLM, and returns a natural language answer.
        """
        if not LLM_INSTANCE or not DB_INSTANCE or not ALL_TABLE_SCHEMAS_STRING:
            return "ERROR: System not initialized. Call initialize_components() first."

        # --- Step 1: Generate SQL Query using LLM ---
        generated_sql_query = ""
        try:
            sql_generation_prompt = f"""
    You are an expert SQL writer. Given the following database schema which contains one or more tables:
    <schema>
    {ALL_TABLE_SCHEMAS_STRING}
    </schema>

    Your task is to generate a single, executable SQL query to answer the question below.
    Question: "{user_question}"

    Important considerations:
    - The question uses "this table," which might be ambiguous if there are multiple tables in the schema.
    - If there are multiple tables, either:
        1. Identify the most prominent or central table (e.g., a table named 'Orders', 'Sales', 'DataCoSupplyChain', or the first table if no other obvious choice).
        2. Or, provide a query that counts rows for all tables, or a representative selection of important ones if there are very many.
    - Output ONLY the SQL query and nothing else. Do not include explanations or markdown formatting like ```sql.

    SQL Query:
    """
            sql_query_response_object = LLM_INSTANCE.invoke(sql_generation_prompt)
            generated_sql_query = sql_query_response_object.content if hasattr(sql_query_response_object, 'content') else str(sql_query_response_object)
            generated_sql_query = generated_sql_query.strip()

            if not generated_sql_query:
                raise ValueError("LLM did not return a SQL query.")
            # print(f"DEBUG: LLM Generated SQL: {generated_sql_query}") # Uncomment for debugging

        except Exception as e:
            return f"ERROR during SQL generation by LLM: {e}"

        # --- Step 2: Execute the Generated SQL Query ---
        sql_query_execution_result = ""
        try:
            sql_query_execution_result = DB_INSTANCE.run(generated_sql_query)
            # print(f"DEBUG: SQL Query Result (raw): {sql_query_execution_result}") # Uncomment for debugging
        except Exception as e:
            return f"ERROR executing SQL query '{generated_sql_query}': {e}. LLM might have generated invalid SQL."

        # --- Step 3: Formulate Natural Language Answer using LLM ---
        try:
            answer_generation_prompt = f"""
    The user originally asked: "{user_question}"
    To answer this, the following SQL query was generated and executed:
    SQL Query: "{generated_sql_query}"
    The result of executing this SQL query was:
    <query_result>
    {sql_query_execution_result}
    </query_result>

    Based on the original question, the SQL query, and its result, provide a concise and clear natural language answer.
    If the query provided counts for multiple tables, summarize that information clearly.
    Answer:
    """
            final_answer_response_object = LLM_INSTANCE.invoke(answer_generation_prompt)
            final_natural_language_answer = final_answer_response_object.content if hasattr(final_answer_response_object, 'content') else str(final_answer_response_object)
            return final_natural_language_answer

        except Exception as e:
            return f"ERROR during final answer generation by LLM: {e}"