

const post = (url, options = {}) => new Promise((resolve, reject) => {
  $.post(url, { 
    ...options,
    timeout: 6000,
    contentType: "application/json;",
    headers: { "Content-Type": "application/json" },
  })
  .done(function (data) {
    console.log({ data });
    resolve(data);
  })
  .fail(function (result) {
    console.log({ result });
    reject(result);
  });
});


var apiHandler = {
  async createLink (link) {
    try {
      const response = await post({ 
        url: 'api.php?token=45c00cf7-5ba0-44b4-91b2-04207685a0a3', 
        dataType: 'json',
        data: {
          api_key: "39c57fbf-3ed3-4a31-b128-0c7db97c994f",
          url: link
        }, 
      });

      return response;
    } catch (error) {
      console.log(error);
      throw new Error('Não foi possível gerar link encurtado.');
    }
  },
};